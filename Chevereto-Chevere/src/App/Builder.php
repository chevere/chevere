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
use Chevere\Contracts\App\BuildContract;
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

final class Builder
{
    /** @var  LoaderContract */
    private $load;

    /** @var Runtime */
    private static $runtime;

    /** @var RequestContract */
    private static $request;

    /** @var AppContract */
    private $app;

    /** @var ParametersContract */
    private $parameters;

    /** @var string */
    private $controller;

    /** @var RouterContract */
    private $router;

    /** @var bool True if run() has been called */
    private $ran;

    /** @var bool True if the console loop ran */
    public $consoleLoop;

    /** @var array */
    private $controllerArguments;

    /** @var Build */
    private $build;

    public function __construct(AppContract $app)
    {
        $this->app = $app;
    }

    public function withParameters(ParametersContract $parameters): Builder
    {
        $new = clone $this;
        $new->parameters = $parameters;

        return $new;
    }
    public function hasParameters(): bool
    {
        return isset($this->parameters);
    }
    public function parameters(): ParametersContract
    {
        return $this->parameters;
    }

    public function withApp(AppContract $app): Builder
    {
        $new = clone $this;
        $new->app = $app;

        return $new;
    }

    public function app(): AppContract
    {
        return $this->app;
    }

    public function withBuild(BuildContract $build): Builder
    {
        $new = clone $this;
        $new->build = $build;

        return $new;
    }
    public function build(): BuildContract
    {
        return $this->build;
    }

    public function withController(string $controller): Builder
    {
        $new = clone $this;
        $new->controller = $controller;

        return $new;
    }
    public function hasController(): bool
    {
        return isset($this->controller);
    }

    public function withControllerArguments(array $arguments): Builder
    {
        $new = clone $this;
        $new->arguments = $arguments;

        return $new;
    }
    public function hasControllerArguments(): bool
    {
        return isset($this->controllerArguments);
    }

    public function withRequest(RequestContract $request): Builder
    {
        $new = clone $this;
        $new::$request = $request;

        return $new;
    }
    public function hasRequest(): bool
    {
        return isset($this->request);
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
            Console::bind($this);
            Console::run();
        }
    }

    private function handleRequest(): void
    {
        if (!isset($this::$request)) {
            $this::$request = ServerRequest::fromGlobals();
        }
    }

    private function processResolveCallable(string $pathInfo): void
    {
        try {
            $route = $this->app->router()->resolve($pathInfo);
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
        $routerArgs = $this->app->router()->arguments();
        if (!isset($this->controllerArguments) && isset($routerArgs)) {
            $this->controllerArguments = $routerArgs;
        }
    }

    private function runApp(string $controller): void
    {
        $this->app = $this->app
            ->withArguments($this->controllerArguments);
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
