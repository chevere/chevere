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
use Chevere\ArrayFile\ArrayFile;
use Chevere\ArrayFile\ArrayFileCallback;
use Chevere\Path\Path;
use Chevere\Path\PathHandle;
use Chevere\Api\Api;
use Chevere\Api\Maker as ApiMaker;
use Chevere\Console\Console;
use Chevere\HttpFoundation\Request;
use Chevere\HttpFoundation\Response;
use Chevere\Router\Maker as RouterMaker;
use Chevere\Runtime\Runtime;
use Chevere\Interfaces\RenderableInterface;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\ParametersContract;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Controllers\Api\HeadController;
use Chevere\Controllers\Api\OptionsController;
use Chevere\Controllers\Api\GetController;
use Chevere\HttpFoundation\Method;
use Chevere\HttpFoundation\Methods;
use Chevere\Api\Endpoint;
use Chevere\Router\Router;
use Chevere\Router\RouterRead;
use Chevere\Type;
use Chevere\Stopwatch;

final class Loader implements LoaderContract
{
    const CACHED = false;

    /** @var Runtime */
    private static $runtime;

    /** @var AppContract */
    public $app;

    /** @var Api */
    private $api;

    /** @var ApiMaker */
    private $apiMaker;

    /** @var string */
    private $controller;

    /** @var Request */
    private $request;

    /** @var Router */
    private $router;
    
    /** @var RouterMaker */
    private $routerMaker;

    /** @var bool True if run() has been called */
    private $ran;

    public function __construct()
    {
        $this->routerMaker = new RouterMaker();
        $this->app = new App();
        $this->app->setResponse(new Response());

        if (false === stream_resolve_include_path(App::BUILD_FILEPATH)) {
            new Checkout(App::BUILD_FILEPATH);
        }

        // Load::php(self::FILEHANDLE_HACKS);
        $pathHandle = new PathHandle(App::FILEHANDLE_PARAMETERS);
        $arrayFile = new ArrayFile($pathHandle);
        $parameters = new Parameters($arrayFile);

        $this->applyParameters($parameters); //router

        if (Console::bind($this)) {
            Console::run();
        }
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
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;

        $pathinfo = ltrim($this->request->getPathInfo(), '/');
        $this->request->attributes->set('requestArray', explode('/', $pathinfo));
    }

    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        if (!isset($this->request)) {
            $this->setRequest(Request::createFromGlobals());
        }

        if (isset($this->ran)) {
            throw new LogicException(
                (new Message('The method %s has been already called.'))
                    ->code('%s', __METHOD__)
                    ->toString()
            );
        }
        $this->ran = true;

        if (!isset($this->controller)) {
            $this->processResolveCallable($this->request->getPathInfo());
        }

        if (isset($this->controller)) {
            $this->runController($this->controller);
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
    public static function request(): Request
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

    private function applyParameters(ParametersContract $parameters)
    {
        $api = $parameters->data->getKey(Parameters::API);
        if (isset($api)) {
            if (static::CACHED) {
                $this->api = new Api();
            } else {
                $this->createApiMaker(new PathHandle($api));
                $this->api = new Api($this->apiMaker);
            }
        }
        $routes = $parameters->data->getKey(Parameters::ROUTES);
        if (isset($routes)) {
            if (static::CACHED) {
                $this->router = new Router();
            } else {
                $this->addRoutes($routes);
                $this->router = new Router($this->routerMaker);
            }
        }
    }

    private function processResolveCallable(string $pathInfo): void
    {
        $this->app->setRoute($this->router->resolve($pathInfo));
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
        if ($controller instanceof RenderableInterface) {
            echo $controller->render();
        } else {
            $this->app->response()->send();
        }
    }

    /** @param array $paramRoutes  'handle' => [Routes,]] */
    private function addRoutes(array $paramRoutes): void
    {
        foreach ($paramRoutes as $fileHandleString) {
            $fileHandle = new PathHandle($fileHandleString);
            $type = new Type(RouteContract::class);
            $arrayFile = new ArrayFile($fileHandle, $type);
            $arrayFileWrap = new ArrayFileCallback($arrayFile, function ($k, $route) {
                $route->setId((string) $k);
            });
            foreach ($arrayFileWrap as $route) {
                $this->routerMaker->addRoute($route, $fileHandleString);
            }
        }
    }

    private function createApiMaker(PathHandle $pathHandle): void
    {
        $this->apiMaker = new ApiMaker($this->routerMaker);
        $methods = new Methods(
            new Method('HEAD', HeadController::class),
            new Method('OPTIONS', OptionsController::class),
            new Method('GET', GetController::class)
        );
        $this->apiMaker->register($pathHandle, new Endpoint($methods)); // 41ms no cache
    }

    // private function processConfigFiles(array $configFiles = null): void
    // {
    //     if (!isset($configFiles)) {
    //         return;
    //     }
    //     if (isset($this->runtime)) {
    //         $this->runtime->runConfig(
    //             (new Config())
    //                 ->processFromFiles($configFiles)
    //         );
    //     }
    // }
}
