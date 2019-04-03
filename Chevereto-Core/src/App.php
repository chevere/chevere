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
use LogicException;
use ReflectionMethod;
use ReflectionFunction;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class App extends Container
{
    use Traits\CallableTrait; // TODO: Implement interfaces

    const NAMESPACES = ['App', __NAMESPACE__];
    const APP = 'app';
    const FILEHANDLE_CONFIG = ':config';
    const FILEHANDLE_PARAMETERS = ':parameters';
    const FILEHANDLE_HACKS = ':hacks';

    protected static $defaultRuntime;
    // protected static $args;

    /** @var bool */
    protected $isCached;

    /** @var array An array containing the plain arguments (scalar data) s */
    protected $arguments = [];
    /** @var array An array containing the prepared controller arguments (object injection) */
    protected $controllerArguments = [];

    // App objects
    protected $runtime;
    protected $logger;
    protected $router;
    protected $request;
    protected $response;
    protected $apis;
    protected $route;
    protected $cache;
    protected $db;
    protected $handler;

    protected $objects = ['runtime', 'config', 'logger', 'router', 'request', 'response', 'apis', 'route', 'cache', 'db', 'handler'];

    public function __construct(AppParameters $parameters = null)
    {
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
            if ($this->hasObject('runtime')) {
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
        $this->setResponse(new Response());
        if (Console::bind($this)) {
            Console::run(); // Note: Console::run() always exit.
        } else {
            $this->setHttpRequest(HttpRequest::createFromGlobals());
        }
    }

    // TODO: Make trait
    public static function hasStaticProp(string $key): bool
    {
        return isset(static::$$key);
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

    // protected function setRuntimeConfig(RuntimeConfig $config): self
    // {
    //     $this->runtimeConfig = $config;

    //     return $this;
    // }

    // public function getRuntimeConfig(): RuntimeConfig
    // {
    //     return $this->runtimeConfig;
    // }

    /**
     * Applies the RuntimeConfig.
     */
    // protected function configure(): self
    // {
    //     if (false == $this->hasObject('runtimeConfig')) {
    //         throw new CoreException(
    //             (new Message('Unable to apply runtimeConfig (no %s has been set).'))
    //                 ->code('%s', RuntimeConfig::class)
    //         );
    //     }
    //     $this->getRuntime()->runConfig($this->getRuntimeConfig());

    //     return $this;
    // }

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
        return $this->request;
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
        // No callable: Resolve HttpRequest then
        if (!isset($callable)) {
            try {
                $route = $this->getRouter()->resolve($this->getHttpRequest()->getPathInfo());
                if (!empty($route)) {
                    $this->setRoute($route);
                    // Resolved callable
                    $callable = $route->getCallable(
                        $this->getHttpRequest()->getMethod()
                    );
                    $this->setArguments(
                        $this->getRouter()->getArguments()
                    );
                } else {
                    echo '404 - Not found';
                }
            } catch (Exception $e) {
                echo 'Exception at App: '.$e->getCode();
            }
        }
        $controller = $this->getControllerObject($callable);
        if ($controller instanceof Interfaces\RenderableInterface) {
            echo $controller->render();
        } else {
            $controller->getResponse()->sendJson();
        }
    }

    /**
     * Runs a explicit provided callable.
     */
    public function getControllerObject(string $callable)
    {
        $controller = $this->getCallable($callable);
        if ($controller instanceof Controller) {
            $controller->setApp($this);
        }
        // HTTP request middleware
        if ($this->route instanceof Route) {
            $middlewares = $this->route->getMiddlewares();
            if (!empty($middlewares)) {
                $handler = new Handler($middlewares);
                $handler->runner($this);
            }
        }
        $controllerType = gettype($controller);
        switch ($controllerType) {
            case 'object':
                $method = '__invoke';
            break;
            case 'string':
                if (Utils\Str::contains('::', $controller)) {
                    $controllerExplode = explode('::', $controller);
                    $controller = $controllerExplode[0];
                    $method = $controllerExplode[1];
                }
            break;
            default:
                throw new LogicException(
                    (string) (new Message('Expecting %s controller type, %t provided for callable string %c.'))
                        ->code('%s', 'invokable object|string')
                        ->code('%t', $controllerType)
                        ->code('%c', $callable)
                );
        }
        if (isset($method)) {
            $reflection = new ReflectionMethod($controller, $method);
        } else {
            $reflection = new ReflectionFunction($controller);
        }
        $controllerArguments = [];
        $parameterIndex = 0;
        // Magically create typehinted objects
        foreach ($reflection->getParameters() as $parameter) {
            $parameterType = $parameter->getType();
            $type = $parameterType != null ? $parameterType->getName() : null;
            $value = $this->arguments[$parameter->getName()] ?? $this->arguments[$parameterIndex] ?? null;
            if ($type === null || in_array($type, Controller::TYPE_DECLARATIONS)) {
                $controllerArguments[] = $value ?? ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null);
            } else {
                // Object typehint
                if ($value === null && $parameter->allowsNull()) {
                    $controllerArguments[] = null;
                } else {
                    $hasConstruct = method_exists($type, '__construct');
                    if (!$hasConstruct) {
                        throw new Exception(
                            (new Message("Class %s doesn't have a constructor. %n %o typehinted in %f invoke function."))
                                ->code('%s', $type)
                                ->code('%o', $type.' $'.$parameter->getName().($parameter->isDefaultValueAvailable() ? ' = '.$parameter->getDefaultValue() : null))
                                ->code('%n', '#'.$parameter->getPosition())
                                ->code('%f', $controller)
                        );
                    }
                    $controllerArguments[] = new $type($value);
                }
            }
            ++$parameterIndex;
        }
        $this->controllerArguments = $controllerArguments;
        $controller(...$this->controllerArguments);

        return $controller;
    }

    /**
     * Farewell kids, my planet needs me.
     */
    // TODO: Stuff
    public function terminate(string $message = null)
    {
        if ($message) {
            Console::log($message);
        }
        // exit();
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
    public function setArguments(array $arguments = [])
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
    public function setControllerArguments(array $arguments = [])
    {
        $this->controllerArguments = $arguments;
    }

    public function getControllerArguments(): array
    {
        return $this->controllerArguments ?? [];
    }

    /**
     * Forges a request (if no Request has been set).
     */
    public function forgeHttpRequest(HttpRequest $request): self
    {
        if ($this->hasObject('request')) {
            throw new CoreException('Unable to forge request when the request has been already set.');
        }
        $this->setHttpRequest($request);

        return $this;
    }

    protected function setHttpRequest(HttpRequest $request): self
    {
        $this->request = $request;
        $pathinfo = ltrim($this->request->getPathInfo(), '/');
        $this->request->attributes->set('requestArray', explode('/', $pathinfo));
        // $host = $_SERVER['HTTP_HOST'] ?? null;
        // $this->define('HTTP_HOST', $host);
        // $this->define('URL', App\HTTP_SCHEME . '://' . $host . ROOT_PATH_RELATIVE);
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
