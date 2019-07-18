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

namespace Chevereto\Chevere;

use Monolog\Logger;
use Chevereto\Chevere\Interfaces\AppInterface;
use Throwable;

/**
 * App contains the whole thing.
 */
class App extends AppStatic implements AppInterface
{
    use Traits\StaticTrait;

    const BUILD_FILEPATH = ROOT_PATH.App\PATH.'build';
    const NAMESPACES = ['App', __NAMESPACE__];
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
    public function __construct(AppParameters $parameters = null)
    {
        static::setStaticInstance($this);
        $this->router = new Router();
        $this->isCached = false;
        if (static::hasStaticProp('defaultRuntime')) {
            $this->runtime = static::getDefaultRuntime();
        }
        if (false === stream_resolve_include_path(static::BUILD_FILEPATH)) {
            new AppCheckout(static::BUILD_FILEPATH);
        }
        Load::php(static::FILEHANDLE_HACKS);
        if (!isset($parameters)) {
            $pathHandle = Path::handle(static::FILEHANDLE_PARAMETERS);
            $parameters = AppParameters::createFromFile($pathHandle);
        }
        $this->processConfigFiles($parameters->getDataKey(AppParameters::CONFIG_FILES));
        $this->processApi($parameters->getDataKey(AppParameters::API));
        $this->processParamRoutes($parameters->getDatakey(AppParameters::ROUTES));
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
        // TODO: Detect valid request (method, etc) - Fails for `php app/console request /`
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
        } else {
            $this->controllerArguments = [];
        }
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
     * Forges a request if no Request has been set.
     */
    public function forgeHttpRequest(HttpRequest $request): self
    {
        if (isset($this->httpRequest)) {
            throw new CoreException('Unable to forge request when the request has been already set.');
        }
        $this->setHttpRequest($request);

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
        if ($controller instanceof Interfaces\RenderableInterface) {
            echo $controller->render();
        } else {
            $controller->getResponse()->send();
        }
    }

    protected function processResolveCallable(string $pathInfo): void
    {
        // try {
        $this->route = $this->router->resolve($pathInfo);
        if (isset($this->route)) {
            $this->callable = $this->route->getCallable(
                    $this->httpRequest->getMethod()
                );
            $routerArgs = $this->router->arguments;
            if (isset($routerArgs)) {
                $this->setArguments($routerArgs);
            }
        } else {
            echo 'NO ROUTE FOUND';

            return;
        }
        // } catch (Throwable $e) {
        //     echo 'Exception at App: '.$e->getCode();

        //     return;
        // }
    }
}
