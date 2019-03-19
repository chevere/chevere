<?php
namespace Chevereto\Core;

define(__NAMESPACE__ . '\TIME_BOOTSTRAP', microtime(true));

/**
 * Assumeing that this file has been loaded from /app/bootstrap.php:
 */
$bootstrapper = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0]['file'];
$ROOT_PATH = rtrim(str_replace('\\', '/', dirname($bootstrapper, 2)), '/') . '/';
$PATH = rtrim(str_replace($ROOT_PATH, null, str_replace('\\', '/', __DIR__)), '/') . '/';
$AppPATH = basename(dirname($bootstrapper)) . '/';

/**
 * Chevereto\Core\ROOT_PATH
 * Root path containing /app
 */
define(__NAMESPACE__ . '\ROOT_PATH', $ROOT_PATH);

/**
 * Chevereto\Core\PATH
 * Relative path to Chevereto\Core, usually 'vendor/chevereto/chevereto-core'
 */
define(__NAMESPACE__ . '\PATH', $PATH);

/**
 * Chevereto\Core\App\PATH
 * Relative path to app, usually 'app'
 */
define(__NAMESPACE__ . '\App\PATH', $AppPATH);

// Namespace handles (adds trailing slashes)
const CORE_NS_HANDLE = __NAMESPACE__ . '\\';
const APP_NS_HANDLE = 'App\\';
// Namespace handle lenghts (hard set)
const NS_HANDLE_LENGTHS = [CORE_NS_HANDLE => 15, APP_NS_HANDLE => 4];

// PHP version checker
require PATH . '/utils/phpcheck.php';
// Must-load classes
require PATH . '/src/Dumper.php';
require PATH . '/src/Console.php';

// Init console if sapi = cli
if (php_sapi_name() == 'cli') {
    Console::init();
}
// This constant allows safe short syntax like `CLI && Console::io()` in all namespaces.
define('CLI', Console::isAvailable());

// TODO: App autoloader solamente!
require PATH . '/autoloader.php';
spl_autoload_register(CORE_NS_HANDLE . 'autoloader');
