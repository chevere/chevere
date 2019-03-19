<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// Documentacion a medias, quizas ordenar un poco.
// Se deberá completar en el camino.
namespace Chevereto\Core;

use Monolog\Logger;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Exception;
use ReflectionMethod;
use ReflectionFunction;
use Symfony\Component\HttpFoundation\JsonResponse;

class App
{
    use Traits\CallableTrait;
    
    const NAMESPACES = ['App', __NAMESPACE__];
    const APP_DEFINITIONS = ['NAME', 'WEBSITE', 'VERSION'];
    const ROOT = 'root';
    const RELATIVE = 'relative';
    const APP = 'app';
    const VENDOR = 'vendor';
    const LOGS = 'logs';
    const FILENAME_HACKS = 'hacks';
    const CONFIG_FILENAME = 'config';
    const ROOT_PATHS = [self::APP => 'app', self::VENDOR => 'vendor'];
    // PATH_* (uppercased)
    const CORE_PATHS = [
        // self::CLASSES => PATH_CLASSES,
        // self::FUNCTIONS => 'functions',
        // self::UTILS => 'utils',
        self::LOGS => 'logs'
    ];

    protected static $instance;
    protected static $args;

    protected $arguments = [];
    protected $controllerArguments = [];
    protected $rootPaths = [];
    protected $paths = [];

    // "Services"
    protected $config; // TODO: Config object
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

    // const SERVICES = ['config', 'logger', 'router', 'request', 'response', 'apis', 'routing', 'route', 'cache', 'db', 'handler'];

    /**
     * @param array $appPaths An array containing app paths (see app/paths.php)
     */
    public function __construct(array $appPaths = null)
    {
        self::$instance = $this;
        // Checkout it app/build exists
        if (stream_resolve_include_path(static::buildFileName()) == false) {
            static::checkout();
        }
        Runtime::setDefaultCharset();
        Load::app(static::FILENAME_HACKS);
        Runtime::fixTimeZone();
        Runtime::registerErrorHandler();
        Config::load();
        Config::apply();
    }
    /**
     * Get the value of handler
     */
    public function getHandler() : Handler
    {
        return $this->handler;
    }
    /**
     * Set the value of handler
     *
     * @return  self
     */
    public function setHandler(Handler $handler)
    {
        $this->handler = $handler;
        return $this;
    }
    // public function setClient(Client $client) : self
    // {
    //     $this->client = $client;
    //     return $this;
    // }
    // public function getClient() : Client
    // {
    //     return $this->client;
    // }
    protected function setRoute(Route $route) : self
    {
        $this->route = $route;
        return $this;
    }
    public function getRoute() : Route
    {
        return $this->route;
    }
    protected function setRouting(Routing $routing) : self
    {
        $this->routing = $routing;
        return $this;
    }
    public function getRouting() : Routing
    {
        return $this->routing;
    }
    public function getRouter() : Router
    {
        return $this->router;
    }
    // FIXME: Need "had" for Route, Routing, Router, Request, Response, Apis
    public function hasRequest() : bool
    {
        return $this->request instanceof Request;
    }
    public function getRequest() : Request
    {
        return $this->request;
    }
    public function getResponse() : Response
    {
        return $this->response;
    }
    public static function buildFileName() : string
    {
        return App\PATH . 'build';
    }
    public function setApis(Apis $apis) : self
    {
        $this->apis = $apis;
        return $this;
    }
    public function getApis() : Apis
    {
        return $this->apis;
    }
    public function getApi(string $key = null) : ?array
    {
        return $this->apis->get($key ?? 'api');
    }
    /**
     * Get build time
     */
    public function buildtime() : ?string
    {
        $filename = $this->buildFileName();
        return File::exists($filename) ? (string) file_get_contents($filename) : null;
    }
    public function checkout() : void
    {
        $filename = $this->buildFileName();
        $fh = @fopen($filename, 'w');
        if (!$fh) {
            throw new Exception(
                (new Message('Unable to open %f for writing'))->code('%f', $filename)
            );
        }
        if (@fwrite($fh, (string) time()) == false) {
            throw new Exception(
                (new Message('Unable to write to %f'))->code('%f', $filename)
            );
        }
        @fclose($fh);
    }
    /**
     * Run the callable and dispatch the handler.
     *
     * @param string $callableSome Controller (path or class name).
     */
    public function run(string $callable = null)
    {
        // TODO: Run should detect if the app misses things needed for running.
        if ($callable == null) {
            try {
                $callable = $this->getRouting()->getController($this->getRequest());
                $this->setRoute($this->getRouting()->getRoute());
            } catch (RouterException $e) {
                dd('APP RUN RESPONSE: ' . $e->getCode());
            }
        }
        $handler = $this->getCallableHandler($callable);
        $handler->send();
    }
    /**
     * Runs a explicit provided callable and return its handler.
     */
    public function getCallableHandler(string $callable)
    {
        $callableString = $callable;
        $callable = $this->getCallable($callableString);
        // HTTP request middleware
        if ($middlewares = $this->route->getMiddlewares()) {
            $handler = new Handler($middlewares);
            $handler->runner($this);
        }
        // Use arguments taken from wildcards
        if ($this->arguments == null) {
            $this->setArguments($this->getRouting()->getArguments());
        }
        if (is_object($callable)) {
            if ($callable instanceof Interfaces\ControllerInterface) {
                $callable->setApp($this);
            }
            $invoke = new ReflectionMethod($callable, '__invoke');
        } else {
            $invoke = new ReflectionFunction($callable);
        }
        $controllerArguments = [];
        $parameterIndex = 0;
        // Magically create typehinted objects
        foreach ($invoke->getParameters() as $parameter) {
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
                                ->code('%o', $type . ' $' . $parameter->getName() . ($parameter->isDefaultValueAvailable() ? ' = ' . $parameter->getDefaultValue() : null))
                                ->code('%n', '#'. $parameter->getPosition())
                                ->code('%f', $callable)
                        );
                    }
                    $controllerArguments[] = new $type($value);
                }
            }
            $parameterIndex++;
        }
        $this->controllerArguments = $controllerArguments;
        $response = $callable(...$this->controllerArguments);
        if ($response instanceof Response || $response instanceof JsonResponse) {
            return $response;
        } else {
            if ($response instanceof \Chevereto\Core\ResponseData) {
                // TODO: Response middelware
                // TODO: Templates
                return $response->generateHttpResponse();
            } else {
                return new JsonResponse($response);
            }
        }
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
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    public function setRouter(Router $router) : self
    {
        if (false == $router->isProcessDone()) {
            $router->processRoutes();
        }
        $this->router = $router;
        $this->routing = new Routing(Routes::instance());
        return $this;
    }
    public function setArguments(array $arguments = null)
    {
        $this->arguments = $arguments;
    }
    public function getArguments() : array
    {
        return $this->arguments;
    }
    // goes before ::run()
    public function setRequest(Request $request) : self
    {
        $this->request = $request;
        $pathinfo = ltrim($this->request->getPathInfo(), '/');
        $this->request->attributes->set('requestArray', explode('/', $pathinfo));
        $host = $_SERVER['HTTP_HOST'] ?? null;
        // $this->define('HTTP_HOST', $host);
        // $this->define('URL', App\HTTP_SCHEME . '://' . $host . ROOT_PATH_RELATIVE);
        return $this;
    }
    public static function instance() : self
    {
        if (self::$instance == null) {
            throw new CoreException('Theres no ' . __CLASS__ .  ' app instance');
        }
        return self::$instance;
    }
    public function getHash() : string
    {
        return ($this->getConstant('App\VERSION') ?: null) . static::buildtime();
    }
    public function getPath(string $key=null, string $group='App') : ?string
    {
    }
    // Sets $paths by group (root, App, Chevereto\Core)
    protected function setPath(string $key, string $group, $var) : void
    {
    }
}
