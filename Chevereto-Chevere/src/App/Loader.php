<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\App;

use const Chevere\CLI;
use const Chevere\DEV;

use LogicException;
use RuntimeException;
use Chevere\ArrayFile\ArrayFile;
use Chevere\Path\PathHandle;
use Chevere\Api\Api;
use Chevere\Console\Console;
use Chevere\Http\Response;
use Chevere\Runtime\Runtime;
use Chevere\Contracts\App\AppContract;
use Chevere\App\Exceptions\NeedsToBeBuiltException;
use Chevere\Cache\Exceptions\CacheNotFoundException;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Contracts\Http\RequestContract;
use Chevere\Contracts\Router\RouterContract;
use Chevere\Http\ServerRequest;
use Chevere\Message\Message;
use Chevere\Router\Exception\RouteNotFoundException;
use Chevere\Contracts\App\ParametersContract;
use Chevere\Contracts\Controller\JsonApiContract;
use Chevere\Router\Router;

use function GuzzleHttp\Psr7\stream_for;

final class Loader implements LoaderContract
{
    /** @var Runtime */
    private static $runtime;

    /** @var RequestContract */
    private static $request;

    /** @var AppContract */
    private $app;

    /** @var ParametersContract */
    private $parameters;

    /** @var Api */
    private $api;

    /** @var string */
    private $controller;

    /** @var RouterContract */
    private $router;

    /** @var bool True if run() has been called */
    private $ran;

    /** @var bool True if the console loop ran */
    private $consoleLoop;

    /** @var array */
    private $arguments;

    /** @var Build */
    private $build;

    public function __construct()
    {
        if (CLI) {
            Console::bind($this);
        }

        $this->build = new Build($this);
        $this->assert();

        $this->app = new App(new Response());

        if (DEV) {
            $this->build = $this->build
                ->withParameters($this->parameters());
        } else {
            try {
                if (Console::isBuilding()) {
                    $api = new Api();
                    $router = new Router();
                } else {
                    $api = Api::fromCache();
                    $router = Router::fromCache();
                }
                $container = $this->build->container()
                    ->withApi($api)
                    ->withRouter($router);
            } catch (CacheNotFoundException $e) {
                throw new NeedsToBeBuiltException(
                    (new Message('The app must be re-build due to missing cache: %message%'))
                        ->code('%message%', $e->getMessage()),
                    $e->getCode(),
                    $e
                );
            }
            $this->build = $this->build
                ->withContainer($container);
        }
        $this->api = $this->build->container()->api();
        $this->router = $this->build->container()->router();
        $this->app = $this->app
            ->withRouter($this->router);
    }

    public function app(): AppContract
    {
        return $this->app;
    }

    public function parameters(): ParametersContract
    {
        if (!isset($this->parameters)) {
            $pathHandle = new PathHandle(App::FILEHANDLE_PARAMETERS);
            $arrayFile = new ArrayFile($pathHandle);
            $this->parameters = new Parameters($arrayFile);
        }

        return $this->parameters;
    }

    public function withBuild(Build $build): LoaderContract
    {
        $new = clone $this;
        $new->build = $build;

        return $new;
    }

    public function build(): Build
    {
        return $this->build;
    }

    /**
     * {@inheritdoc}
     */
    public function withController(string $controller): LoaderContract
    {
        $new = clone $this;
        $new->controller = $controller;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withArguments(array $arguments): LoaderContract
    {
        $new = clone $this;
        $new->arguments = $arguments;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withRequest(RequestContract $request): LoaderContract
    {
        $new = clone $this;
        $new::$request = $request;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        $this->handleConsole();
        $this->handleRequest();
        if (isset($this->ran)) {
            throw new LogicException(
                (new Message('The method %s has been already called.'))
                    ->code('%s', __METHOD__)
                    ->toString()
            );
        }
        $this->ran = true;
        if (!isset($this->controller)) {
            $this->processResolveCallable($this::$request->getUri()->getPath());
        }
        if (!isset($this->controller)) {
            throw new RuntimeException('DESCONTROL');
        }
        $this->runApp($this->controller);
    }

    /**
     * {@inheritdoc}
     */
    public static function runtime(): Runtime
    {
        return self::$runtime;
    }

    /**
     * {@inheritdoc}
     */
    public static function request(): RequestContract
    {
        return self::$request;
    }

    /**
     * {@inheritdoc}
     */
    public static function setDefaultRuntime(Runtime $runtime)
    {
        self::$runtime = $runtime;
    }

    private function handleConsole(): void
    {
        if (CLI && !isset($this->consoleLoop)) {
            $this->consoleLoop = true;
            Console::run();
        }
    }

    private function handleRequest(): void
    {
        if (!isset($this::$request)) {
            $this::$request = ServerRequest::fromGlobals();
        }
    }

    private function assert(): void
    {
        if (!DEV && !Console::isBuilding() && !$this->build->exists()) {
            throw new NeedsToBeBuiltException(
                (new Message('The application needs to be built by CLI %command% or calling %method% method.'))
                    ->code('%command%', 'php app/console build')
                    ->code('%method%', __CLASS__ . '::' . 'build')
                    ->toString()
            );
        }
    }

    private function processResolveCallable(string $pathInfo): void
    {
        try {
            $route = $this->router->resolve($pathInfo);
        } catch (RouteNotFoundException $e) {
            $response = $this->app->response();
            $guzzle = $response->guzzle()->withStatus(404)->withBody(stream_for('Not found.'));
            $response = $response->withGuzzle($guzzle);
            $this->app = $this->app
                ->withResponse($response);
            if (CLI) {
                throw new RouteNotFoundException($e->getMessage());
            } else {
                $this->app->response()->sendHeaders()->sendBody();
                die();
            }
        }
        $this->controller = $route->getController($this::$request->getMethod());
        $this->app = $this->app
            ->withRoute($route);
        $routerArgs = $this->router->arguments();
        if (!isset($this->arguments) && isset($routerArgs)) {
            $this->arguments = $routerArgs;
        }
    }

    private function runApp(string $controller): void
    {
        $this->app = $this->app
            ->withArguments($this->arguments);
        $runner = new Runner($this->app);
        $controller = $runner->runController($controller);
        $contentStream = stream_for($controller->content());
        $response = $this->app->response();
        $guzzle = $response->guzzle();
        $response = $response->withGuzzle(
            $controller instanceof JsonApiContract
                ? $guzzle->withJsonApi($contentStream)
                : $guzzle->withBody($contentStream)
        );
        $this->app = $this->app
            ->withResponse($response);
        if (!CLI) {
            $this->app->response()
                ->sendHeaders()
                ->sendBody();
        }
    }
}
