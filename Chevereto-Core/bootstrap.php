<?php

declare(strict_types=1);

namespace Chevereto\Core;

define(__NAMESPACE__.'\TIME_BOOTSTRAP', microtime(true));
define(__NAMESPACE__.'\ERROR_LEVEL_BOOTSTRAP', error_reporting());

// Namespace handles (adds trailing slashes)
const CORE_NS_HANDLE = __NAMESPACE__.'\\';
const APP_NS_HANDLE = 'App\\';

/*
 * Assuming that this file has been loaded from /app/bootstrap.php
 */
define('Chevereto\Core\BOOTSTRAPPER', debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0]['file']);

/*
 * Chevereto\Core\ROOT_PATH
 * Root path containing /app
 */
define('Chevereto\Core\ROOT_PATH', rtrim(str_replace('\\', '/', dirname(BOOTSTRAPPER, 2)), '/').'/');

/*
 * Chevereto\Core\PATH
 * Relative path to Chevereto\Core, usually 'vendor/chevereto/chevereto-core'
 */
define('Chevereto\Core\PATH', rtrim(str_replace(ROOT_PATH, null, str_replace('\\', '/', __DIR__)), '/').'/');

/*
 * Chevereto\Core\App\PATH
 * Relative path to app, usually 'app'
 */
define('Chevereto\Core\App\PATH', basename(dirname(BOOTSTRAPPER)).'/');

// Init console if sapi = cli
if (php_sapi_name() == 'cli') {
    Console::init();
}

const DEFAULT_ERROR_HANDLING = [
    RuntimeConfig::DEBUG => 1,
    RuntimeConfig::ERROR_HANDLER => 'Chevereto\Core\ErrorHandler::error',
    RuntimeConfig::EXCEPTION_HANDLER => 'Chevereto\Core\ErrorHandler::exception',
];

/*
 * Default error and exception handler
 */
new Runtime(
    (new RuntimeConfig())
        ->processFromArray(DEFAULT_ERROR_HANDLING)
);

// This constant allows safe short syntax like `CLI && Console::io()` in all namespaces.
define('Chevereto\Core\CLI', Console::isRunning());

/*
 * Kickstand
 */
App::setDefaultRuntime(
    new Runtime(
        (new RuntimeConfig())
            ->addArray([
                RuntimeConfig::LOCALE => 'en_US.UTF8',
                RuntimeConfig::DEFAULT_CHARSET => 'utf-8',
                RuntimeConfig::TIMEZONE => 'UTC',
                RuntimeConfig::URI_SCHEME => 'https',
            ] + DEFAULT_ERROR_HANDLING)
            ->addFile(App::FILEHANDLE_CONFIG)
            ->process()
    )
);
