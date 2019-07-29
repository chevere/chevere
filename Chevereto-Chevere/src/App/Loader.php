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
use Chevere\ArrayFile;
use Chevere\Path;
use Chevere\Api\Api;
use Chevere\Api\Maker as ApiMaker;
use Chevere\Console\Console;
use Chevere\HttpFoundation\Request;
use Chevere\Route\ArrayFileWrap as RouteArrayFileWrap;
use Chevere\Route\Route;
use Chevere\Router\Router;
use Chevere\Runtime\Runtime;
use Chevere\Interfaces\RenderableInterface;

final class Loader
{
    /** @var Runtime */
    private static $runtime;

    /** @var App */
    public $app;

    /** @var ApiMaker */
    private $apiMaker;

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
        $this->app = new App($this);

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
        } else {
            $this->setRequest(Request::createFromGlobals());
        }
    }

    private function applyParameters(Parameters $parameters)
    {
        // $this->processConfigFiles($parameters->getDataKey(Parameters::CONFIG_FILES));
        $api = $parameters->getDataKey(Parameters::API);
        if (isset($api)) {
            $this->processApi($api);
        }
        $routes = $parameters->getDatakey(Parameters::ROUTES);
        if (isset($routes)) {
            $this->processRoutes($routes);
        }
    }

    public static function setDefaultRuntime(Runtime $runtime)
    {
        self::$runtime = $runtime;
    }

    /**
     * Forges a Request, wrapper for Symfony Request::create().
     *
     * @param string               $uri        The URI
     * @param string               $method     The HTTP method
     * @param array                $parameters The query (GET) or request (POST) parameters
     * @param array                $cookies    The request cookies ($_COOKIE)
     * @param array                $files      The request files ($_FILES)
     * @param array                $server     The server parameters ($_SERVER)
     * @param string|resource|null $content    The raw body data
     */
    public function forgeHttpRequest(...$requestArguments): void
    {
        if (isset($this->request)) {
            throw new LogicException('Unable to forge request when the request has been already set.');
        }
        if (!in_array($requestArguments[1], Route::HTTP_METHODS)) {
            throw new LogicException(
                (new Message('Unknown HTTP request method %s'))
                    ->code('%s', $requestArguments[1])
                    ->toString()
            );
        }
        $this->setRequest(Request::create(...$requestArguments));
    }

    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    public function run(): void
    {
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
            $this->processController($this->controller);
        }
    }

    private function setRequest(Request $request): void
    {
        $this->request = $request;

        $pathinfo = ltrim($this->request->getPathInfo(), '/');
        $this->request->attributes->set('requestArray', explode('/', $pathinfo));
    }

    private function processResolveCallable(string $pathInfo): void
    {
        // try {
        $this->app->route = $this->router->resolve($pathInfo);
        $this->controller = $this->app->route->getCallable($this->request->getMethod());
        $routerArgs = $this->router->arguments;
        // dd($routerArgs);
        if (isset($routerArgs)) {
            $this->setArguments($routerArgs);
        }
        // } catch (Throwable $e) {
        //     echo 'Exception at App: '.$e->getCode();

        //     return;
        // }
    }

    /**
     * @param array $arguments string arguments captured or injected
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    public static function runtime(): Runtime
    {
        if (isset(self::$runtime)) {
            return self::$runtime;
        }
        throw new RuntimeException('NO RUNTIME INSTANCE EVERYTHING BURNS!');
    }

    public static function request(): Request
    {
        if (isset(self::$request)) {
            return self::$request;
        }
        throw new RuntimeException('NO REQUEST INSTANCE EVERYTHING BURNS!');
    }

    private function processController(string $controller): void
    {
        $controller = $this->app->getControllerObject($controller);
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
            foreach ((new RouteArrayFileWrap($fileHandle))->getArrayFile()->toArray() as $k => $route) {
                $this->router->addRoute($route, $fileHandleString);
            }
        }
    }

    private function processApi(string $pathIdentifier): void
    {
        $this->apiMaker = new ApiMaker($this->router);
        $this->apiMaker->register($pathIdentifier);
        $this->app->api = new Api($this->apiMaker);
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
