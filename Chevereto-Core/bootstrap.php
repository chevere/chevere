<?php
namespace Chevereto\Core;

use Chevereto\Core\App;

define(__NAMESPACE__ . '\TIME_BOOTSTRAP', microtime(true));
define(__NAMESPACE__ . '\ERROR_LEVEL_BOOTSTRAP', error_reporting());

/**
 * Assumeing that this file has been loaded from /app/bootstrap.php:
 */
$bootstrapper = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0]['file'];
$ROOT_PATH = rtrim(str_replace('\\', '/', dirname($bootstrapper, 2)), '/') . '/';
$PATH = rtrim(str_replace($ROOT_PATH, null, str_replace('\\', '/', __DIR__)), '/') . '/';
$AppPATH = basename(dirname($bootstrapper)) . '/';

// Namespace handles (adds trailing slashes)
const CORE_NS_HANDLE = __NAMESPACE__ . '\\';
const APP_NS_HANDLE = 'App\\';
// Namespace handle lenghts (hard set)
const NS_HANDLE_LENGTHS = [CORE_NS_HANDLE => 15, APP_NS_HANDLE => 4];

/**
 * Chevereto\Core\ROOT_PATH
 * Root path containing /app
 */
define(CORE_NS_HANDLE . 'ROOT_PATH', $ROOT_PATH);

/**
 * Chevereto\Core\PATH
 * Relative path to Chevereto\Core, usually 'vendor/chevereto/chevereto-core'
 */
define(CORE_NS_HANDLE . 'PATH', $PATH);

/**
 * Chevereto\Core\App\PATH
 * Relative path to app, usually 'app'
 */
define(CORE_NS_HANDLE . 'App\PATH', $AppPATH);

// PHP version checker
// require PATH . '/utils/phpcheck.php';

// Init console if sapi = cli
if (php_sapi_name() == 'cli') {
    Console::init();
}

/**
 * Initiate the default runtime settings
 */
define(CORE_NS_HANDLE . 'RUNTIME_DEFAULTS', App::runtimeDefaults());

// FIXME: Better way to register dd & dump functions
new Dumper();


// This constant allows safe short syntax like `CLI && Console::io()` in all namespaces.
define('CLI', Console::isAvailable());

// TODO: Composer autoload para App
require PATH . '/autoloader.php';
// Failover loader
spl_autoload_register(CORE_NS_HANDLE . 'autoloader');
