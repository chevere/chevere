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

use Symfony\Component\Console\Application as ConsoleApp;
use Symfony\Component\HttpFoundation\Response;

use Exception;
use ReflectionMethod;
use ReflectionFunction;

class App
{
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

    protected $logger;
    protected $router;
    protected $request;
    protected $response;
    protected $apis;
    protected $routing;
    protected $route;

    public function get(string $id)
    {
    }
    // public function has(string $id) : bool {
    //     return
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
    // FIXME: Need "had" for Route, Routing, Router, Http\Request, Http\Response, Apis
    public function hasRequest() : bool
    {
        return $this->request instanceof Http\Request;
    }
    public function getRequest() : Http\Request
    {
        return $this->request;
    }
    public function getResponse() : Http\Response
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
     * Run the callable.
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
        if (isset($this->route->middleware)) {
            $request = $this->request;
            $response = $this->response;
            foreach ($this->route->middleware as $k => $v) {
                $v($callable, function ($request, $response, $next) {
                    Console::writeln('Next closure stuff here...');
                });
            }
        }
        Console::writeln('About to get response...');
        $response = $this->runner($callable);
        $response->send();
    }
    public function runner($callable) : Response
    {
        $classExists = class_exists($callable);
        $isCallable = is_callable($callable);
        if ($classExists || $isCallable) {
            $callable = $classExists ? new $callable : $callable;
        } else {
            $callable = include $callable;
        }
        if (is_callable($callable) == false) {
            throw new Exception(
                (new Message('Expected %s callable, %t provided.'))
                    ->code('%s', '$callable')
                    ->code('%t', gettype($callable))
            );
        }
        // Arguments taken directly from wildcards
        if ($this->arguments == null) {
            $this->setArguments($this->getRouting()->getArguments());
        }
        $i = 0;
        if (is_object($callable)) {
            $invoke = new ReflectionMethod($callable, '__invoke');
        } else {
            $invoke = new ReflectionFunction($callable);
        }
        $orderedArguments = [];
        // Magically create typehinted objects
        foreach ($invoke->getParameters() as $parameter) {
            $rType = $parameter->getType();
            $type = $rType != null ? $rType->getName() : null;
            $value = $this->arguments[$parameter->getName()] ?? $this->arguments[$i] ?? null;
            if ($type == null || in_array($type, Controller::TYPE_DECLARATIONS)) {
                $orderedArguments[] = $value ?? ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null);
            } else {
                // Object typehint
                if ($value === null && $parameter->allowsNull()) {
                    $orderedArguments[] = null;
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
                    $orderedArguments[] = new $type($value);
                }
            }
            $i++;
        }
        /**
         * Controller gets called here.
         */
        // $middlewares = [true, true, false];
        // foreach ($middlewares as $condition) {
        //     if ($condition === false) {
        //         dd('FALSE CONDITION DIE!');
        //     }
        // }
        $this->controllerArguments = $orderedArguments;
        $output = $callable(...$this->controllerArguments);
        if ($output instanceof Http\Response || $output instanceof Http\JsonResponse) {
            $response = $output;
        } else {
            $response = new Http\JsonResponse($output);
        }
        return $response;
    }
    /**
     * Farewell kids, my planet needs me.
     */
    public function terminate()
    {
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
        $this->response = new Http\Response();
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
    public function setRequest(Http\Request $request) : self
    {
        $this->request = $request;
        $pathinfo = ltrim($this->request->getPathInfo(), '/');
        $this->request->attributes->set('requestArray', explode('/', $pathinfo));
        return $this;
    }
    public function setRequestFromGlobals()
    {
        $this->setRequest(Http\Request::createFromGlobals());
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
