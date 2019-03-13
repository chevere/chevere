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

class App
{
    use Traits\CallableTrait;
    
    const NAMESPACES = ['App', __NAMESPACE__];
    const APP_DEFINITIONS = ['NAME', 'WEBSITE', 'VERSION'];
    const ROOT = 'root';
    const RELATIVE = 'relative';
    const APP = 'app';
    const VENDOR = 'vendor';
    const CLASSES = 'classes';
    const FUNCTIONS = 'functions';
    const LOGS = 'logs';
    const UTILS = 'utils';
    const FILENAME_HACKS = 'hacks';
    const FILENAME_BASE_FUNCTIONS = 'base';
    const CONFIG_FILENAME = 'config';
    const ROOT_PATHS = [self::APP => 'app', self::VENDOR => 'vendor'];
    const CORE_PATH_ALIAS = 'core>';
    // PATH_* (uppercased)
    const CORE_PATHS = [
        self::CLASSES => PATH_CLASSES,
        self::FUNCTIONS => 'functions',
        self::UTILS => 'utils',
        self::LOGS => 'logs'
    ];

    protected static $instance;
    protected static $args;

    protected $arguments = [];
    protected $controllerArguments = [];
    protected $rootPaths;
    protected $paths;

    // "Services"
    protected $config;
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

    // public function setConfig(Config $config) : self
    // {
    //     $this->config = $config;
    //     return $this;
    // }
    // public function getConfig() : Config
    // {
    //     return $this->config;
    // }
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
        return PATH_APP . 'build';
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
     * Run callable and send the response.
     *
     * @param string $callableSome Controller (path or class name).
     */
    public function run(string $callable = null)
    {
        if ($callable == null) {
            try {
                $callable = $this->getRouting()->getController($this->getRequest());
                $this->setRoute($this->getRouting()->getRoute());
            } catch (RouterException $e) {
                dd('APP RUN RESPONSE: ' . $e->getCode());
            }
        }
        $response = $this->runner($callable);
        $response->send();
    }
    /**
     * Run a callable and return its response.
     */
    public function runner(string $callableString)
    {
        $callable = $this->getCallable($callableString);
        // HTTP request middleware
        if ($middlewares = $this->route->getMiddlewares()) {
            // foreach ($middlewares as $k => $v) {
            //     dump('Middleware', $this->getCallable($v));
            // }
            $handler = new Handler($middlewares);
            $runner = $handler->runner($this);
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
        $output = $callable(...$this->controllerArguments);
        if ($output instanceof Response || $output instanceof JsonResponse) {
            $response = $output;
        } else {
            $response = new JsonResponse($output);
        }
        return $response;
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
    /**
     * @param array $appPaths An array containing app paths (see app/paths.php)
     */
    public function __construct(array $appPaths = null)
    {
        self::$instance = $this;
        // $this->response = new Response();
        // uses $this->request:
        static::setBasePaths();
        // Checkout it app/build exists
        if (stream_resolve_include_path(static::buildFileName()) == false) {
            static::checkout();
        }
        // Clone App definitions (app/app.php) to Chevereto\Core\App
        foreach (static::APP_DEFINITIONS as $v) {
            static::define(__NAMESPACE__ . '\App\\' . $v, constant('\App\\' . $v));
        }
        // If no $appPaths, set it from default paths file
        if ($appPaths === null) {
            $appPaths = Load::app('paths');
        }
        Runtime::setDefaultCharset();
        Load::app(static::FILENAME_HACKS);
        Runtime::fixTimeZone();
        // Runtime::fixServer();
        Runtime::registerErrorHandler();
        Config::load();
        Config::apply();
        static::define('HTTP_SCHEME', Config::get(Config::HTTP_SCHEME));
        if ($appPaths !== null) {
            static::setPaths($appPaths);
        }
        // Uses $this->request:
        static::defineHttpStuff();
    }
    public function setRouter(Router $router) : self
    {
        $this->router = $router;
        $this->routing = new Routing(Routes::instance());
        return $this;
    }
    public function setArguments(array $arguments = null)
    {
        $this->arguments = $arguments ?? [];
    }
    public function getArguments() : ?array
    {
        return $this->arguments ?? [];
    }
    // goes before ::run()
    public function setRequest(Request $request) : self
    {
        $this->request = $request;
        $pathinfo = ltrim($this->request->getPathInfo(), '/');
        $this->request->attributes->set('requestArray', explode('/', $pathinfo));
        return $this;
    }
    // public function setRequestFromGlobals()
    // {
    //     $this->setRequest(Request::createFromGlobals());
    // }
    public static function instance() : self
    {
        if (self::$instance == null) {
            throw new CoreException('Theres no ' . __CLASS__ .  ' app instance');
        }
        return self::$instance;
    }
    public function getHash() : string
    {
        return ($this->constant('App\VERSION') ?: null) . static::buildtime();
    }
    public function path(string $key=null, string $group='App') : ?string
    {
        return $this->paths()[$group][$key];
    }
    public function setPaths(array $appPaths) : void
    {
        // Ignore custom app classes path
        $appPaths[static::CLASSES] = $this->constant('PATH_CLASSES');
        $keyClass = array_search(static::CLASSES, $appPaths);
        if ($keyClass !== false) {
            unset($appPaths[$keyClass]);
        }
        // Sets and defines App\PATH_* based on loader paths array
        foreach ($appPaths as $k => $v) {
            $this->setPath(is_int($k) ? $v : $k, 'App', $v);
        }
        ksort($this->paths);
    }
    public function definitions() : array
    {
        $definitions = [];
        $raw = get_defined_constants(true)['user'];
        foreach ($raw as $k => $v) {
            if (Utils\Str::startsWith(APP_NS_HANDLE, $k) || Utils\Str::startsWith(CORE_NS_HANDLE, $k)) {
                $definitions[$k] = $v;
            }
        }
        ksort($definitions);
        return $definitions;
    }
    // Define a constant in both App and Chevereto\Core namespace
    public function define(string $name, $value) : void
    {
        $queue = [];
        if (strpos($name, '\\') === false) {
            $name = APP_NS_HANDLE . $name;
        }
        $queue = [$name => $value];
        // Dupe App\Definitions to Chevereto\Core\App\Definitions, exlude ROOT_ stuff
        if (Utils\Str::startsWith(APP_NS_HANDLE, $name) && !Utils\Str::startsWith(APP_NS_HANDLE . strtoupper(static::ROOT) . '_', $name)) {
            $queue[__NAMESPACE__ . "\\$name"] = $value;
        }
        foreach ($queue as $name => $value) {
            if (defined($name)) {
                continue;
            }
            define($name, $value);
        }
    }
    // Sets the base system paths (root + core)
    protected function setBasePaths() : void
    {
        // Sets $rootPaths and defines Chevereto\Core\* paths
        $this->setRootPath(__NAMESPACE__, PATH); // PATH (Chevereto\Core path)
        // FIXME: Windows symlinks
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->setRootPath(static::VENDOR, dirname(PATH) . '/vendor'); // ROOT_PATH_VENDOR
        } else {
            $this->setRootPath(static::VENDOR, dirname(dirname(PATH))); // ROOT_PATH_VENDOR
        }
        $this->setRootPath(static::ROOT, dirname(ROOT_PATH_VENDOR)); // ROOT_PATH
        // The concept of "relative" path doesn't exists in CLI
        if (php_sapi_name() != 'cli') {
            $relative = dirname($_SERVER['SCRIPT_NAME']);
        } else {
            $relative = '__CLI__';
        }
        $this->setRootPath(static::RELATIVE, $relative); // ROOT_PATH_RELATIVE
        $this->setRootPath('App', ROOT_PATH . static::ROOT_PATHS[static::APP]); // ROOT_PATH_APP
        foreach (static::ROOT_PATHS as $k => $v) {
            $this->setPath($k, static::ROOT, $v);
        }
        // Defines Chevereto\Core\PATH_* (see static::CORE_PATHS)
        foreach (static::CORE_PATHS as $k => $v) {
            $this->setPath($k, __NAMESPACE__, $v);
        }
    }
    // Returns $paths[$group]
    public function paths(string $group=null) : ?array
    {
        if ($group == null) {
            return $this->paths;
        }
        $paths = $this->paths();
        if ($paths != null && !array_key_exists($group, $paths)) {
            throw new Exception(
                (new Message('Group parameter %s not found in paths array'))->code('%s', $group)
            );
        }
        return $paths[$group];
    }
    // Sets $paths by group (root, App, Chevereto\Core)
    protected function setPath(string $key, string $group, $var) : void
    {
        $varType = gettype($var);
        // TODO: Strong typing needed here
        if ($varType == 'array') {
            if (count($var) !== 1) {
                throw new Exception('Argument #2 count must be 1 if you want to use an array');
            }
            reset($var);
            $firstKey = key($var);
            $aux = $this->path($firstKey, $group);
            $root = isset($aux) ? $aux : $firstKey;
            $path = $root . (string) $var[$firstKey];
        } elseif ($varType == 'string') {
            $root = $this->rootPaths[$group];
            if (strpos($var, $root) === false) {
                $path = $root . $var;
            } else {
                $path = $var;
            }
        } else {
            throw new Exception(
                (new Message('Argument #2 must be string or array, %s provided'))->code('%s', $varType)
            );
        }
        $this->paths[$group][$key] = Sanitize::path($path);
        if (in_array($group, static::NAMESPACES)) {
            $groupHandle = $group . '\PATH_' . strtoupper(str_replace('/', '_', $key));
            if (defined($groupHandle) == false) {
                $this->define($groupHandle, $this->path($key, $group));
            }
        }
    }
    // Returns $rootPaths[$key]
    public function rootPath(string $key=null) : string
    {
        if ($key === null) {
            $key = static::ROOT;
        }
        return $this->rootPaths()[$key];
    }
    // Returns $rootPaths
    public function rootPaths() : array
    {
        return $this->rootPaths;
    }
    // Populates $rootPaths
    protected function setRootPath(string $namespace, string $val) : void
    {
        if ($this->rootPaths && array_key_exists($namespace, $this->rootPaths)) {
            throw new Exception(
                (new Message('Cannot redeclare %s root path'))->code('%s', $namespace)
            );
        }
        $value = Sanitize::path($val);
        $this->rootPaths[$namespace] = $value;
        if (in_array($namespace, static::NAMESPACES)) {
            $handle = 'PATH';
        } else {
            $handle = static::ROOT . '_PATH';
            $aux = str_replace('\\', '_', $namespace);
            if ($aux !== static::ROOT) {
                $handle .= '_' . $aux;
            }
        }
        // App namespace PATHS
        $appHandle = null;
        if ($handle == 'PATH' && $namespace == 'App') {
            $handle = $namespace . '\\' . strtoupper($handle); // App\PATH
            $appHandle = $handle;
            $aux = true;
        } else {
            $handle = strtoupper($handle);
        }
        $coreHandle = Core::namespaced($handle);
        // Defines path at Chevereto\Core namespace
        if (defined($coreHandle) == false) {
            $this->define($coreHandle, $value);
        }
        if (isset($aux)) {
            $auxHandle = $appHandle ?: APP_NS_HANDLE . $handle;
            // Defines path at App namespace
            if (defined($auxHandle) == false) {
                $this->define($auxHandle, $value);
            }
        }
    }
    public function defineHttpStuff()
    {
        $host = $_SERVER['HTTP_HOST'] ?? null;
        $this->define('HTTP_HOST', $host);
        $this->define('URL', App\HTTP_SCHEME . '://' . $host . ROOT_PATH_RELATIVE);
    }
    public function constant(string $name, string $namespace = 'App') : ?string
    {
        $constant = "\\$namespace\\$name";
        return defined($constant) ? constant($constant) : null;
    }
    public function url(string $path = null) : string
    {
        return $this->constant('URL') . $path;
    }
}