<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere;

use Chevere\App\App;
use Chevere\App\Loader;
use Chevere\Console\Console;
use Chevere\Runtime\Runtime;
use Chevere\Runtime\Sets\RuntimeSetDebug;
use Chevere\Runtime\Sets\RuntimeSetDefaultCharset;
use Chevere\Runtime\Sets\RuntimeSetPrecision;
use Chevere\Runtime\Sets\RuntimeSetTimeZone;
use Chevere\Runtime\Sets\RuntimeSetUriScheme;
use Chevere\Runtime\Sets\RuntimeSetLocale;
use Chevere\Runtime\Sets\RuntimeSetErrorHandler;
use Chevere\Runtime\Sets\RuntimeSetExceptionHandler;

/** DEV_MODE true rebuild the App on every load */
define('Chevere\DEV_MODE', true);

/*
 * Assuming that this file has been loaded from /app/bootstrap.php
 */

define('Chevere\BOOTSTRAPPER', debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file']);

/* Root path containing /app */
define('Chevere\ROOT_PATH', rtrim(str_replace('\\', '/', dirname(BOOTSTRAPPER, 2)), '/') . '/');

/*
 * Chevere\PATH
 * Relative path to Core, usually 'vendor/chevereto/chevereto-core'
 */
define('Chevere\PATH', rtrim(str_replace(ROOT_PATH, null, str_replace('\\', '/', __DIR__)), '/') . '/');

/* Relative path to app, usually 'app' */
define('Chevere\APP_PATH_RELATIVE', basename(dirname(BOOTSTRAPPER)) . '/');
define('Chevere\APP_PATH', ROOT_PATH . APP_PATH_RELATIVE);

if ('cli' == php_sapi_name()) {
    Console::init(); //10ms
}

define('Chevere\CLI', Console::isRunning());

// $sw = new Stopwatch();
Loader::setDefaultRuntime(
    new Runtime(
        new RuntimeSetDebug('1'), // 0.2ms
        new RuntimeSetErrorHandler('Chevere\ErrorHandler\ErrorHandler::error'), // 0.9ms
        new RuntimeSetExceptionHandler('Chevere\ErrorHandler\ErrorHandler::exception'), // 0.5ms
        new RuntimeSetLocale('en_US.UTF8'), // 0.2ms
        new RuntimeSetDefaultCharset('utf-8'), // 0.2ms
        new RuntimeSetPrecision('16'), // 0.2ms
        new RuntimeSetUriScheme('https'), // 0.2ms
        new RuntimeSetTimeZone('UTC') // 1.85
    )
); // 0.6ms wrapper

// $sw->stop();
// dd($sw->records(), 'BOOTSTRAP');

    // ->addFile(App::FILEHANDLE_CONFIG)
