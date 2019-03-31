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
use ReflectionMethod;
use ReflectionFunction;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

// use Symfony\Component\HttpFoundation\Response;

class App extends Container
{
    use Traits\CallableTrait;

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
    protected $routing;
    protected $route;
    protected $cache;
    protected $db;
    protected $handler;

    protected $objects = ['runtime', 'config', 'logger', 'router', 'request', 'response', 'apis', 'routing', 'route', 'cache', 'db', 'handler'];

    public function __construct(AppParameters $parameters = null)
    {
        $this->setRouter(new Router());
        $this->isCached = false;
        if (static::hasStaticProp('defaultRuntime')) {
            $this->setRuntime(static::getDefaultRuntime());
        }
        if (stream_resolve_include_path($this->getBuildFilePath()) == false) {
            $this->checkout();
        }
        Load::php(static::FILEHANDLE_HACKS);
        if (null == $parameters) {
            $arrayFile = new ArrayFile(static::FILEHANDLE_PARAMETERS, 'array');
            try {
            } catch (Exception $e) {
                throw new CoreException($e);
            }
            $parameters = new AppParameters($arrayFile->toArray());
        }
        if ($configFiles = $parameters->getDataKey(AppParameters::CONFIG_FILES)) {
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
        if ($paramApis = $parameters->getDataKey(AppParameters::APIS)) {
            $apis = new Apis($this->getRouter());
            if (false == $this->isCached) {
                foreach ($paramApis as $apiKey => $apiPath) {
                    $apis->register($apiKey, $apiPath);
                }
            }
            $this->setApis($apis);
        }
        if ($paramRoutes = $parameters->getDatakey(AppParameters::ROUTES)) {
            // ['handle' => [Routes,]]
            foreach ($paramRoutes as $fileHandle) {
                foreach ((new Routes($fileHandle))->getArrayFile()->toArray() as $k => $route) {
                    $this->getRouter()->addRoute($route, $fileHandle);
                }
            }
        }
        if (Console::bind($this)) {
            Console::run(); // Note: Console::run() always exit.
        } else {
            $this->setRequest(Request::createFromGlobals());
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

    protected function setRouting(Routing $routing): self
    {
        $this->routing = $routing;

        return $this;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    // FIXME: Must be protected
    public function setResponse(Response $response): self
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
        $fh = @fopen($filename, 'w');
        if (!$fh) {
            throw new RuntimeException(
                (string) (new Message('Unable to open %f for writing'))->code('%f', $filename)
            );
        }
        if (@fwrite($fh, (string) time()) == false) {
            throw new RuntimeException(
                (string) (new Message('Unable to write to %f'))->code('%f', $filename)
            );
        }
        if (false == @fclose($fh)) {
            throw new RuntimeException(
                (string) (new Message('Unable to close %f'))->code('%f', $filename)
            );
        }
    }

    /**
     * Run the callable and dispatch the handler.
     *
     * @param string $callable controller (path or class name)
     */
    public function run(string $callable = null)
    {
        // TODO: Run should detect if the app misses things needed for running.
        if (null == $callable) {
            try {
                $this->setRoute(
                    $this->getRouter()->resolve($this->getRequest()->getPathInfo())
                );
                $callable = $this->getRoute()->getCallable(
                    $this->getRequest()->getMethod()
                );
                $this->setArguments(
                    $this->getRouter()->getArguments()
                );
            } catch (RouterException $e) {
                die('APP RUN RESPONSE: '.$e->getCode());
            }
        }
        if (null != $callable) {
            $controller = $this->getControllerObject($callable);
            if ($controller instanceof Interfaces\RenderableInterface) {
                echo $controller->render();
            } else {
                $controller->getResponse()->sendJson();
            }
        }
    }

    /**
     * Runs a explicit provided callable.
     */
    public function getControllerObject(string $callable)
    {
        // $this->setResponse(new Response());
        $controller = $this->getCallable($callable);
        if ($controller instanceof Controller) {
            $controller->setApp($this);
        }
        // HTTP request middleware
        // TODO: Re-Check
        if ($this->route instanceof Route && $middlewares = $this->route->getMiddlewares()) {
            $handler = new Handler($middlewares);
            $handler->runner($this);
        }
        if (is_object($controller)) {
            $method = '__invoke';
        } else {
            if (Utils\Str::contains('::', $controller)) {
                $controllerExplode = explode('::', $controller);
                $controller = $controllerExplode[0];
                $method = $controllerExplode[1];
            }
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
            if ($type == null || in_array($type, Controller::TYPE_DECLARATIONS)) {
                $controllerArguments[] = $value ?? ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null);
            } else {
                // Object typehint
                if ($value === null && $parameter->allowsNull()) {
                    $controllerArguments[] = null;
                } else {
                    $hasConstruct = method_exists($type, '__construct');
                    if ($hasConstruct == false) {
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
    public function terminate(string $message = null)
    {
        if ($message) {
            Console::log($message);
        }
        exit();
    }

    // FIXME: Must be protected
    public function setLogger(Logger $logger)
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
    public function forgeRequest(Request $request): self
    {
        if ($this->hasObject('request')) {
            throw new CoreException('Unable to forge request when the request has been already set.');
        }
        $this->setRequest($request);

        return $this;
    }

    protected function setRequest(Request $request): self
    {
        $this->request = $request;
        $pathinfo = ltrim($this->request->getPathInfo(), '/');
        $this->request->attributes->set('requestArray', explode('/', $pathinfo));
        $host = $_SERVER['HTTP_HOST'] ?? null;
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
