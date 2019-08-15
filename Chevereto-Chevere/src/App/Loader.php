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
use Chevere\Api\Maker;
use Chevere\Console\Console;
use Chevere\HttpFoundation\Request;
use Chevere\HttpFoundation\Response;
use Chevere\Router\Router;
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
use Chevere\Type;

final class Loader implements LoaderContract
{
    /** @var Runtime */
    private static $runtime;

    /** @var AppContract */
    public $app;

    /** @var string */
    private $controller;

    /** @var Request */
    private $request;

    /** @var Router */
    private $router;

    /** @var bool True if run() has been called */
    private $ran;

    public function __construct()
    {
        $this->router = new Router();
        $this->app = new App();
        $this->app->response = new Response();

        if (false === stream_resolve_include_path(App::BUILD_FILEPATH)) {
            new Checkout(App::BUILD_FILEPATH);
        }

        // Load::php(self::FILEHANDLE_HACKS);
        $pathHandle = Path::handle(App::FILEHANDLE_PARAMETERS);
        $arrayFile = new ArrayFile($pathHandle);
        $parameters = new Parameters($arrayFile);

        $this->applyParameters($parameters);

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
        // $this->processConfigFiles($parameters->data->getKey(Parameters::CONFIG_FILES));
        $api = $parameters->data->getKey(Parameters::API);
        if (isset($api)) {
            $pathHandle = Path::handle($api);
            $this->processApi($pathHandle);
        }
        $routes = $parameters->data->getKey(Parameters::ROUTES);
        if (isset($routes)) {
            $this->processRoutes($routes);
        }
    }

    private function processResolveCallable(string $pathInfo): void
    {
        $this->app->route = $this->router->resolve($pathInfo);
        $this->controller = $this->app->route->getController($this->request->getMethod());
        $routerArgs = $this->router->arguments();
        if (!isset($this->arguments) && isset($routerArgs)) {
            $this->setArguments($routerArgs);
        }
    }

    private function runController(string $controller): void
    {
        $this->app->arguments = $this->arguments;
        $controller = $this->app->run($controller);
        if ($controller instanceof RenderableInterface) {
            echo $controller->render();
        } else {
            $this->app->response->send();
        }
    }

    private function processRoutes(array $paramRoutes): void
    {
        // ['handle' => [Routes,]]
        foreach ($paramRoutes as $fileHandleString) {
            $fileHandle = Path::handle($fileHandleString);
            $type = new Type(RouteContract::class);
            $arrayFile = new ArrayFile($fileHandle, $type);
            $arrayFileWrap = new ArrayFileCallback($arrayFile, function ($k, $route) {
                $route->setId((string) $k);
            });
            foreach ($arrayFileWrap as $k => $route) {
                $this->router->addRoute($route, $fileHandleString);
            }
        }
    }

    private function processApi(PathHandle $pathHandle): void
    {
        $maker = new Maker($this->router);
        $methods = new Methods(
            new Method('HEAD', HeadController::class),
            new Method('OPTIONS', OptionsController::class),
            new Method('GET', GetController::class)
        );
        $maker->register($pathHandle, new Endpoint($methods));
        $this->app->api = new Api($maker);
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
