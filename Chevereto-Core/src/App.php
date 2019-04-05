<?php

declare(strict_types=1);

/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use RuntimeException;
use Exception;
use Monolog\Logger;

/**
 * @method string hasRuntime(): bool
 * @method string hasLogger(): bool
 * @method string hasRouter(): bool
 * @method string hasHttpRequest(): bool
 * @method string hasResponse(): bool
 * @method string hasApis(): bool
 * @method string hasRoute(): bool
 * //@method string hasCache(): bool
 * //@method string hasDb(): bool
 * @method string hasHandler(): bool
 */
class App extends Container
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

    /** @var array The propName => ClassName map for the Container */
    protected $objects = [
        'runtime' => Runtime::class,
        'logger' => Logger::class,
        'router' => Router::class,
        'httpRequest' => HttpRequest::class,
        'response' => Response::class,
        'apis' => Apis::class,
        'route' => Route::class,
        'cache' => 'Cache::class',
        'db' => 'Db::class',
        'handler' => Handler::class,
    ];

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

    /** @var Apis */
    protected $apis;

    /** @var Route */
    protected $route;

    /** @var string */
    protected $cache;

    /** @var string */
    protected $db;

    /** @var Handler */
    protected $handler;

    public function __construct(AppParameters $parameters = null)
    {
        static::$instance = $this;
        $this->setRouter(new Router());
        $this->isCached = false;
        if (static::hasStaticProp('defaultRuntime')) {
            $this->setRuntime(static::getDefaultRuntime());
        }
        if (false === stream_resolve_include_path($this->getBuildFilePath())) {
            $this->checkout();
        }
        Load::php(static::FILEHANDLE_HACKS);
        if (null == $parameters) {
            $arrayFile = new ArrayFile(static::FILEHANDLE_PARAMETERS, 'array');
            $parameters = new AppParameters($arrayFile->toArray());
        }
        $configFiles = $parameters->getDataKey(AppParameters::CONFIG_FILES);
        if (isset($configFiles)) {
            if (isset($this->runtime)) {
                $this->getRuntime()->runConfig(
                    (new RuntimeConfig())
                        ->processFromFiles($configFiles)
                );
            }
        }
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
        $paramApis = $parameters->getDataKey(AppParameters::APIS);
        if (isset($paramApis)) {
            $apis = new Apis($this->getRouter());
            if (!$this->isCached) {
                foreach ($paramApis as $apiKey => $apiPath) {
                    $apis->register($apiKey, $apiPath);
                }
            }
            $this->setApis($apis);
        }
        $paramRoutes = $parameters->getDatakey(AppParameters::ROUTES);
        if (isset($paramRoutes)) {
            // ['handle' => [Routes,]]
            foreach ($paramRoutes as $fileHandle) {
                foreach ((new Routes($fileHandle))->getArrayFile()->toArray() as $k => $route) {
                    $this->getRouter()->addRoute($route, $fileHandle);
                }
            }
        }
        dd($this->getRouter()->getRoutes());
        $this->setResponse(new Response());
        if (Console::bind($this)) {
            Console::run(); // Note: Console::run() always exit.
        } else {
            $this->setHttpRequest(HttpRequest::createFromGlobals());
        }
    }

    public static function hasInstance(): bool
    {
        return isset(static::$instance);
    }

    /**
     * Provides access to the App HttpRequest instance.
     *
     * @return HttpRequest|null
     */
    public static function requestInstance(): ?HttpRequest
    {
        if (isset(static::$instance)) {
            // Request isn't there when doing cli (unless you run the request command)
            if (isset(static::$instance->httpRequest)) {
                return static::$instance->getHttpRequest();
            }
        }

        return null;
    }

    /**
     * Provides access to the App Runtime instance.
     *
     * @return Runtime|null
     */
    public static function runtimeInstance(): ?Runtime
    {
        if (isset(static::$instance)) {
            if (isset(static::$instance->runtime)) {
                return static::$instance->getRuntime();
            }
        }

        return null;
    }

    protected function setRuntime(Runtime $runtime): self
    {
        $this->runtime = $runtime;

        return $this;
    }

    public function getRuntime(): Runtime
    {
        return $this->runtime;
    }

    /**
     * Get the value of handler.
     */
    public function getHandler(): Handler
    {
        return $this->handler;
    }

    /**
     * Set the value of handler.
     *
     * @return self
     */
    protected function setHandler(Handler $handler)
    {
        $this->handler = $handler;

        return $this;
    }

    protected function setRoute(Route $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getHttpRequest(): HttpRequest
    {
        return $this->httpRequest;
    }

    protected function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public static function getBuildFilePath(): string
    {
        return ROOT_PATH.App\PATH.'build';
    }

    protected function setApis(Apis $apis): self
    {
        $this->apis = $apis;

        return $this;
    }

    public function getApis(): Apis
    {
        return $this->apis;
    }

    public function getApi(string $key = null): ?array
    {
        return $this->apis->get($key ?? 'api');
    }

    /**
     * Get build time.
     */
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
            try {
                $route = $this->getRouter()->resolve($this->getHttpRequest()->getPathInfo());
                if (!empty($route)) {
                    $this->setRoute($route);
                    $callable = $route->getCallable(
                        $this->getHttpRequest()->getMethod()
                    );
                    $this->setArguments(
                        $this->getRouter()->getArguments()
                    );
                } else {
                    echo '404 - Not found';

                    return;
                }
            } catch (Exception $e) {
                echo 'Exception at App: '.$e->getCode();

                return;
            }
        }
        if (isset($callable)) {
            $controller = $this->getControllerObject($callable);
            if ($controller instanceof Interfaces\RenderableInterface) {
                echo $controller->render();
            } else {
                $controller->getResponse()->send();
            }
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
        $controller = $callableWrap->getCallable();

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
            $callableWrap->setPassedArguments($this->getArguments());
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

    protected function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    protected function setRouter(Router $router): self
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Sets the plain App arguments (scalar data).
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Sets the rich controller arguments (object injection).
     */
    public function setControllerArguments(array $arguments)
    {
        $this->controllerArguments = $arguments;
    }

    public function getControllerArguments(): array
    {
        return $this->controllerArguments;
    }

    /**
     * Forges a request (if no Request has been set).
     */
    public function forgeHttpRequest(HttpRequest $request): self
    {
        if (isset($this->httpRequest)) {
            throw new CoreException('Unable to forge request when the request has been already set.');
        }
        $this->setHttpRequest($request);

        return $this;
    }

    protected function setHttpRequest(HttpRequest $request): self
    {
        $this->httpRequest = $request;
        $pathinfo = ltrim($this->httpRequest->getPathInfo(), '/');
        $this->httpRequest->attributes->set('requestArray', explode('/', $pathinfo));

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

    public static function setDefaultRuntime(Runtime $runtime): void
    {
        static::$defaultRuntime = $runtime;
    }

    public static function getDefaultRuntime(): Runtime
    {
        return static::$defaultRuntime;
    }
}
