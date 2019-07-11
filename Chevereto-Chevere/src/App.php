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
use RuntimeException;
use Throwable;

/**
 * App contains the whole thing.
 */
class App implements AppInterface
{
    use Traits\StaticTrait;

    const NAMESPACES = ['App', __NAMESPACE__];
    const APP = 'app';
    const FILEHANDLE_CONFIG = ':config';
    const FILEHANDLE_PARAMETERS = ':parameters';
    const FILEHANDLE_HACKS = ':hacks';

    protected static $instance;
    protected static $defaultRuntime;

    /** @var bool */
    protected $isCached;

    /** @var array An array containing string arguments (from request uri, cli) */
    protected $arguments;

    /** @var array An array containing the prepared controller arguments (object injection) */
    protected $controllerArguments;

    /** @var Runtime */
    protected $runtime;

    /** @var Logger */
    protected $logger;

    /** @var Router */
    protected $router;

    /** @var HttpRequest */
    protected $httpRequest;

    /** @var Response */
    protected $response;

    /** @var Api */
    protected $api;

    /** @var Route */
    protected $route;

    /** @var string */
    protected $cache;

    /** @var string */
    protected $db;

    /** @var Handler */
    protected $handler;

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
        static::$instance = $this;
        $this->setRouter(new Router());
        $this->isCached = false;
        if (static::hasStaticProp('defaultRuntime')) {
            $this->setRuntime(static::getDefaultRuntime());
        }
        $this->processCheckout();
        Load::php(static::FILEHANDLE_HACKS);
        if (!isset($parameters)) {
            $pathHandle = Path::handle(static::FILEHANDLE_PARAMETERS);
            $parameters = AppParameters::createFromFile($pathHandle);
        }
        $this->processConfigFiles($parameters->getDataKey(AppParameters::CONFIG_FILES));
        $this->processApi($parameters->getDataKey(AppParameters::API));
        $this->processParamRoutes($parameters->getDatakey(AppParameters::ROUTES));
        $this->setResponse(new Response());
        $this->processSapi();
    }

    public function getBuildTime(): ?string
    {
        $filename = $this->getBuildFilePath();

        return File::exists($filename) ? (string) file_get_contents($filename) : null;
    }

    public function checkout(): void
    {
        $filename = $this->getBuildFilePath();
        $fh = fopen($filename, 'w');
        if (false === $fh) {
            throw new RuntimeException(
                (string) (new Message('Unable to open %f for writing'))->code('%f', $filename)
            );
        }
        if (!@fwrite($fh, (string) time())) {
            throw new RuntimeException(
                (string) (new Message('Unable to write to %f'))->code('%f', $filename)
            );
        }
        if (!@fclose($fh)) {
            throw new RuntimeException(
                (string) (new Message('Unable to close %f'))->code('%f', $filename)
            );
        }
    }

    /**
     * Run the callable and dispatch the handler.
     *
     * @param string $callable controller, needed when doing console command or testing
     */
    public function run(string $callable = null)
    {
        if (!isset($callable)) {
            // TODO: Detect valid request (method, etc) - Fails for `php app/console request /`
            $this->routerResolve($this->httpRequest->getPathInfo());
        } else {
            $this->callable = $callable;
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

    protected function setRuntime(Runtime $runtime): self
    {
        $this->runtime = $runtime;

        return $this;
    }

    protected function setLogger(Logger $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    protected function setRouter(Router $router): self
    {
        $this->router = $router;

        return $this;
    }

    protected function setHttpRequest(HttpRequest $request): self
    {
        $this->httpRequest = $request;

        $pathinfo = ltrim($this->httpRequest->getPathInfo(), '/');
        $this->httpRequest->attributes->set('requestArray', explode('/', $pathinfo));

        return $this;
    }

    protected function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    protected function setApi(Api $api): self
    {
        $this->api = $api;

        return $this;
    }

    protected function setRoute(Route $route): self
    {
        $this->route = $route;

        return $this;
    }

    protected function setHandler(Handler $handler)
    {
        $this->handler = $handler;

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

    public function getArguments(): ?array
    {
        return $this->arguments ?? null;
    }

    /**
     * @param array $arguments Prepared controller arguments
     */
    public function setControllerArguments(array $arguments): AppInterface
    {
        $this->controllerArguments = $arguments;

        return $this;
    }

    public function getControllerArguments(): ?array
    {
        return $this->controllerArguments ?? null;
    }

    public function getRuntime(): ?Runtime
    {
        return $this->runtime ?? null;
    }

    public function getLogger(): ?Logger
    {
        return $this->logger ?? null;
    }

    public function getRouter(): ?Router
    {
        return $this->router ?? null;
    }

    public function getHttpRequest(): ?HttpRequest
    {
        return $this->httpRequest ?? null;
    }

    public function getResponse(): ?Response
    {
        return $this->response ?? null;
    }

    public function getApi(): ?Api
    {
        return $this->api ?? null;
    }

    public function getRoute(): ?Route
    {
        return $this->route ?? null;
    }

    public function getHandler(): ?Handler
    {
        return $this->handler ?? null;
    }

    protected function processCheckout(): void
    {
        if (false === stream_resolve_include_path($this->getBuildFilePath())) {
            $this->checkout();
        }
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
        $this->setApi($api);
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

    protected function routerResolve(string $pathInfo): void
    {
        try {
            $route = $this->router->resolve($pathInfo);
            if (isset($route)) {
                $this->setRoute($route);
                $this->callable = $route->getCallable(
                    $this->httpRequest->getMethod()
                );
                $routerArgs = $this->router->getArguments();
                if (isset($routerArgs)) {
                    $this->setArguments($routerArgs);
                }
            } else {
                echo 'NO ROUTE FOUND';

                return;
            }
        } catch (Throwable $e) {
            echo 'Exception at App: '.$e->getCode();

            return;
        }
    }

    public static function getBuildFilePath(): string
    {
        return ROOT_PATH.App\PATH.'build';
    }

    public static function setDefaultRuntime(Runtime $runtime): void
    {
        static::$defaultRuntime = $runtime;
    }

    public static function getDefaultRuntime(): Runtime
    {
        return static::$defaultRuntime;
    }

    /**
     * Provides access to the App HttpRequest instance.
     *
     * @return HttpRequest|null
     */
    public static function requestInstance(): ?HttpRequest
    {
        // Request isn't there when doing cli (unless you run the request command)
        return isset(static::$instance) && isset(static::$instance->httpRequest)
            ? static::$instance->httpRequest
            : null;
    }

    /**
     * Provides access to the App Runtime instance.
     *
     * @return Runtime|null
     */
    public static function runtimeInstance(): ?Runtime
    {
        if (isset(static::$instance) && $runtimeInstance = static::$instance->getRuntime()) {
            return $runtimeInstance;
        }

        return null;
    }
}
