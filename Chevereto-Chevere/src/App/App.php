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

namespace Chevereto\Chevere\App;

use Throwable;
use LogicException;
use const Chevereto\Chevere\ROOT_PATH;
use const Chevereto\Chevere\App\PATH as AppPath;
use Monolog\Logger;
use Chevereto\Chevere\Console;
use Chevereto\Chevere\Runtime;
use Chevereto\Chevere\Router;
use Chevereto\Chevere\HttpRequest;
use Chevereto\Chevere\Response;
use Chevereto\Chevere\Api\Api;
use Chevereto\Chevere\File;
use Chevereto\Chevere\Path;
use Chevereto\Chevere\Controller\Controller;
use Chevereto\Chevere\Load;
use Chevereto\Chevere\Route\Route;
use Chevereto\Chevere\Routes;
use Chevereto\Chevere\CallableWrap;
use Chevereto\Chevere\Message;
use Chevereto\Chevere\RuntimeConfig;
use Chevereto\Chevere\Interfaces\AppInterface;
use Chevereto\Chevere\Interfaces\RenderableInterface;
use Chevereto\Chevere\Traits\StaticTrait;

/**
 * App contains the whole thing.
 */
class App extends AppStatic implements AppInterface
{
    use StaticTrait;

    const BUILD_FILEPATH = ROOT_PATH.AppPath.'build';
    const NAMESPACES = ['App', 'Chevereto\Chevere'];
    const APP = 'app';
    const FILEHANDLE_CONFIG = ':config';
    const FILEHANDLE_PARAMETERS = ':parameters';
    const FILEHANDLE_HACKS = ':hacks';

    /** @var bool */
    protected $isCached;

    /** @var array|null An array containing string arguments (from request uri, cli) */
    public $arguments;

    /** @var array|null An array containing the prepared controller arguments (object injection) */
    public $controllerArguments;

    /** @var Runtime */
    public $runtime;

    /** @var Logger */
    public $logger;

    /** @var Router */
    public $router;

    /** @var HttpRequest */
    public $httpRequest;

    /** @var Response */
    public $response;

    /** @var Api */
    public $api;

    /** @var Route */
    public $route;

    /** @var string */
    protected $cache;

    /** @var string */
    protected $db;

    /** @var string */
    protected $callable;

    /*
    * (A) Router cache : The array which is used to resolve /req -> route (routing)
    * (B) Routes cache : The array of serialized Routes ['id' => Route serialized]
    * (C) Apis cache : The array containing the exposed API
    * ...
    * CHECK IF APP IS CACHED UNDER THE PROVIDED APIS+ROUTES
    * ...
    * new App:
    * 1. setParams (Runtime, [apis], [routes])
    * 2. isCached
    *      ? Router && API from Cache
    *      : Router && API on-the-fly
    * 3. Resolve controller
    *      - Router -> maps route id -> get Route -> return callable
    *
    * - Provide route access with some helper like Route::get('homepage@routes:web') which gets name=homepage from routes/web.php
    * - app/console dump:routes route:web -> Shows the return (generated objects) of this file
    * - App autoinjects a "Router", which could be Router::fromCache(...) or new Router() and provides access to Routes (cached or new)
    * - RouteCollection contains the array of mapped routes (objects or serialized arrays (cached))
    */
    public function __construct(Parameters $parameters = null)
    {
        static::setStaticInstance($this);
        $this->router = new Router();
        $this->isCached = false;
        if (static::hasStaticProp('defaultRuntime')) {
            $this->runtime = static::getDefaultRuntime();
        }
        if (false === stream_resolve_include_path(static::BUILD_FILEPATH)) {
            new Checkout(static::BUILD_FILEPATH);
        }
        Load::php(static::FILEHANDLE_HACKS);
        if (!isset($parameters)) {
            $pathHandle = Path::handle(static::FILEHANDLE_PARAMETERS);
            $parameters = Parameters::createFromFile($pathHandle);
        }
        $this->processConfigFiles($parameters->getDataKey(Parameters::CONFIG_FILES));
        $this->processApi($parameters->getDataKey(Parameters::API));
        $this->processParamRoutes($parameters->getDatakey(Parameters::ROUTES));
        $this->response = new Response();
        $this->processSapi();
    }

    public function getBuildTime(): ?string
    {
        return File::exists(static::BUILD_FILEPATH) ? (string) file_get_contents(static::BUILD_FILEPATH) : null;
    }

    public function setCallable(string $callable): self
    {
        $this->callable = $callable;

        return $this;
    }

    /**
     * Run the callable and dispatch the handler.
     *
     * @param string $callable controller, needed when doing console command or testing
     */
    public function run()
    {
        if (!isset($this->callable)) {
            $this->processResolveCallable($this->httpRequest->getPathInfo());
        }
        if (isset($this->callable)) {
            $this->processCallable($this->callable);
        }
    }

    /**
     * Runs a explicit provided callable string.
     *
     * @param string $callable function or method name
     */
    public function getControllerObject(string $callable)
    {
        $callableWrap = new CallableWrap($callable);
        $controller = $callableWrap->callable;

        if ($controller instanceof Controller) {
            $controller->setApp($this);
        }

        // if ($this->route instanceof Route) {
        //     $middlewares = $this->route->getMiddlewares();
        //     if (!empty($middlewares)) {
        //         $handler = new Handler($middlewares);
        //         $handler->runner($this);
        //     }
        // }

        if (!empty($this->arguments)) {
            $callableWrap->setPassedArguments($this->arguments);
            $this->controllerArguments = $callableWrap->getArguments();
        // dd($callableWrap);
        } else {
            $this->controllerArguments = [];
        }
        // dd($this->arguments, $this->controllerArguments);
        $controller(...$this->controllerArguments);

        return $controller;
    }

    /**
     * Farewell kids, my planet needs me.
     */
    public function terminate(string $message = null)
    {
        if ($message) {
            Console::log($message);
        }
        // callTermEvent();
    }

    /**
     * Forges an HttpRequest, wrapper for Symfony HttpRequest::create().
     *
     * @param string               $uri        The URI
     * @param string               $method     The HTTP method
     * @param array                $parameters The query (GET) or request (POST) parameters
     * @param array                $cookies    The request cookies ($_COOKIE)
     * @param array                $files      The request files ($_FILES)
     * @param array                $server     The server parameters ($_SERVER)
     * @param string|resource|null $content    The raw body data
     */
    public function forgeHttpRequest(...$requestArguments): self
    {
        if (isset($this->httpRequest)) {
            throw new LogicException('Unable to forge request when the request has been already set.');
        }
        if (!in_array($requestArguments[1], Route::HTTP_METHODS)) {
            throw new LogicException(
                (string) (new Message('Unknown HTTP request method %s'))->code('%s', $requestArguments[1])
            );
        }
        $this->setHttpRequest(HttpRequest::create(...$requestArguments));

        return $this;
    }

    public function getHash(): string
    {
        return ($this->getConstant('App\VERSION') ?: null).$this->getBuildTime();
    }

    public function getConstant(string $name, string $namespace = 'App'): ?string
    {
        $constant = "\\$namespace\\$name";

        return defined($constant) ? constant($constant) : null;
    }

    protected function setHttpRequest(HttpRequest $request): self
    {
        $this->httpRequest = $request;

        $pathinfo = ltrim($this->httpRequest->getPathInfo(), '/');
        $this->httpRequest->attributes->set('requestArray', explode('/', $pathinfo));

        return $this;
    }

    /**
     * @param array $arguments string arguments captured or injected
     */
    public function setArguments(array $arguments): AppInterface
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @param array $arguments Prepared controller arguments
     */
    public function setControllerArguments(array $arguments): AppInterface
    {
        $this->controllerArguments = $arguments;

        return $this;
    }

    protected function processConfigFiles(array $configFiles = null): void
    {
        if (!isset($configFiles)) {
            return;
        }
        if (isset($this->runtime)) {
            $this->runtime->runConfig(
                (new RuntimeConfig())
                    ->processFromFiles($configFiles)
            );
        }
    }

    protected function processApi(string $pathIdentifier = null): void
    {
        if (!isset($pathIdentifier)) {
            return;
        }
        $api = new Api($this->router);
        if (!$this->isCached) {
            $api->register($pathIdentifier);
        }
        $this->api = $api;
    }

    protected function processParamRoutes(array $paramRoutes = null): void
    {
        if (!isset($paramRoutes)) {
            return;
        }
        // ['handle' => [Routes,]]
        foreach ($paramRoutes as $fileHandleString) {
            $fileHandle = Path::handle($fileHandleString);
            foreach ((new Routes($fileHandle))->getArrayFile()->toArray() as $k => $route) {
                $this->router->addRoute($route, $fileHandleString);
            }
        }
    }

    protected function processSapi(): void
    {
        if (Console::bind($this)) {
            Console::run(); // Note: Console::run() always exit.
        } else {
            $this->setHttpRequest(HttpRequest::createFromGlobals());
        }
    }

    protected function processCallable(string $callable): void
    {
        $controller = $this->getControllerObject($callable);
        if ($controller instanceof RenderableInterface) {
            echo $controller->render();
        } else {
            $controller->getResponse()->send();
        }
    }

    protected function processResolveCallable(string $pathInfo): void
    {
        // try {
        $this->route = $this->router->resolve($pathInfo);
        $this->callable = $this->route->getCallable($this->httpRequest->getMethod());
        $routerArgs = $this->router->arguments;
        if (isset($routerArgs)) {
            $this->setArguments($routerArgs);
        }
        // } catch (Throwable $e) {
        //     echo 'Exception at App: '.$e->getCode();

        //     return;
        // }
    }
}
