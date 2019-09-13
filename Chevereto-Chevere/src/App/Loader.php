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

use LogicException;
use RuntimeException;

use const Chevere\CLI;
use const Chevere\DEV;
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
use Chevere\Contracts\Render\RenderContract;
use Chevere\Contracts\Router\RouterContract;
use Chevere\Http\ServerRequest;
use Chevere\Message;
use Chevere\Router\Exception\RouteNotFoundException;
use Chevere\Router\Router;

final class Loader implements LoaderContract
{
    /** @var Runtime */
    private static $runtime;

    /** @var AppContract */
    public $app;

    /** @var Api */
    private $api;

    /** @var string */
    private $controller;

    /** @var RequestContract */
    private $request;

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

        $this->app = new App();
        $this->app->setResponse(new Response());

        if (DEV) {
            $this->build->make(
                $this->parameters()
            );
        } else {
            $this->build->apply();
        }

        $this->api = $this->build->container()->api();
        $this->router = $this->build->container()->router();
        $this->app->setRouter($this->router);
    }

    public function parameters(): Parameters
    {
        if (!isset($this->parameters)) {
            $pathHandle = new PathHandle(App::FILEHANDLE_PARAMETERS);
            $arrayFile = new ArrayFile($pathHandle);
            $this->parameters = new Parameters($arrayFile);
        }
        return $this->parameters;
    }

    public function build(): Build
    {
        return $this->build;
    }

    /**
     * {@inheritdoc}
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function setArguments(array $arguments): LoaderContract
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(RequestContract $request): void
    {
        $this->request = $request;
        // $pathinfo = ltrim((string) $this->request->getUri(), '/');
        // $pathinfo = $this->request->getUri()->getPath();
        // $this->request->attributes->set('requestArray', explode('/', $pathinfo));
        $this->app->setRequest($this->request);
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
            $this->processResolveCallable($this->request->getUri()->getPath());
        }
        if (!isset($this->controller)) {
            throw new RuntimeException('DESCONTROL');
        }
        $this->runController($this->controller);
    }

    private function handleConsole()
    {
        if (CLI && !isset($this->consoleLoop)) {
            $this->consoleLoop = true;
            Console::run();
        }
    }

    private function handleRequest()
    {
        if (!isset($this->request)) {
            $this->setRequest(ServerRequest::fromGlobals());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function runtime(): Runtime
    {
        if (isset(self::$runtime)) {
            return self::$runtime;
        }
        throw new RuntimeException('NO RUNTIME INSTANCE EVERYTHING BURNS!');
    }

    /**
     * {@inheritdoc}
     */
    public static function request(): RequestContract
    {
        if (isset(self::$request)) {
            return self::$request;
        }
        throw new RuntimeException('NO REQUEST INSTANCE EVERYTHING BURNS!');
    }

    /**
     * {@inheritdoc}
     */
    public static function setDefaultRuntime(Runtime $runtime)
    {
        self::$runtime = $runtime;
    }

    private function assert() : void
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
            $this->app->response()->setStatusCode(404);
            $this->app->response()->setContent('404');
            $this->app->response()->prepare($this->app->request());
            $this->app->response()->send();
            if (CLI) {
                throw new RouteNotFoundException($e->getMessage());
            } else {
                die();
            }
        }
        $this->app->setRoute($route);
        $this->controller = $this->app->route()->getController($this->request->getMethod());
        $routerArgs = $this->router->arguments();
        if (!isset($this->arguments) && isset($routerArgs)) {
            $this->setArguments($routerArgs);
        }
    }

    private function runController(string $controller): void
    {
        $this->app->setArguments($this->arguments);
        $controller = $this->app->run($controller);
        if ($controller instanceof RenderContract) {
            $controller->render();
        } else {
            $jsonApi = $controller->document();
            $this->app->setResponse(
                $this->app->response()
                    ->withJsonApi($jsonApi)
            );
            if (!CLI) {
                $this->app->response()
                    ->sendHeaders()
                    ->sendBody();
            }
        }
    }
}
