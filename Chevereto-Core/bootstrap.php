<?php
namespace Chevereto\Core;

use Chevereto\Core\App;
use Chevereto\Core\Config;

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

// Init console if sapi = cli
if (php_sapi_name() == 'cli') {
    Console::init();
}

const DEFAULT_ERROR_HANDLING = [
    Config::ERROR_REPORTING_LEVEL => E_ALL ^ E_NOTICE,
    Config::ERROR_HANDLER => CORE_NS_HANDLE . 'ErrorHandler::error',
    Config::EXCEPTION_HANDLER => CORE_NS_HANDLE . 'ErrorHandler::exception',
];

/**
 * Default error and exception handler
 */
new Runtime(
    (new Config())
        ->processFromArray(DEFAULT_ERROR_HANDLING)
);

/**
 * Kickstand
 */
App::setDefaultRuntime(
    new Runtime(
        (new Config())
            ->addArray([
                Config::DEBUG => 0,
                Config::LOCALE => 'en_US.UTF8',
                Config::DEFAULT_CHARSET => 'utf-8',
                Config::TIMEZONE => 'UTC',
                Config::URI_SCHEME => 'https',
                Config::TIMEZONE => 'UTC',
            ] + DEFAULT_ERROR_HANDLING)
            ->addFile(':config')
            ->process()
    )
);

// This constant allows safe short syntax like `CLI && Console::io()` in all namespaces.
define('CLI', Console::isRunning());

// TODO: Composer autoload para App
require PATH . '/autoloader.php';
// Failover loader
spl_autoload_register(CORE_NS_HANDLE . 'autoloader');
