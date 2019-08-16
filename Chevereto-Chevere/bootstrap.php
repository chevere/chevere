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

$Stopwatch = new \Chevere\Stopwatch();

define(__NAMESPACE__.'\ERROR_LEVEL_BOOTSTRAP', error_reporting());

/*
 * Assuming that this file has been loaded from /app/bootstrap.php
 */
define('Chevere\BOOTSTRAPPER', debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file']);

/*
 * Chevere\ROOT_PATH
 * Root path containing /app
 */
define('Chevere\ROOT_PATH', rtrim(str_replace('\\', '/', dirname(BOOTSTRAPPER, 2)), '/').'/');

/*
 * Chevere\PATH
 * Relative path to Core, usually 'vendor/chevereto/chevereto-core'
 */
define('Chevere\PATH', rtrim(str_replace(ROOT_PATH, null, str_replace('\\', '/', __DIR__)), '/').'/');

/*
 * Chevere\App\PATH
 * Relative path to app, usually 'app'
 */
define('Chevere\App\PATH', basename(dirname(BOOTSTRAPPER)).'/');

if ('cli' == php_sapi_name()) {
    Console::init(); //10ms
}

define('Chevere\CLI', Console::isRunning());

new Runtime(
    new RuntimeSetDebug('1'),
    new RuntimeSetErrorHandler('Chevere\ErrorHandler\ErrorHandler::error'),
    new RuntimeSetExceptionHandler('Chevere\ErrorHandler\ErrorHandler::exception'),
); // 1ms

Loader::setDefaultRuntime(
    new Runtime(
        new RuntimeSetLocale('en_US.UTF8'),
        new RuntimeSetDefaultCharset('utf-8'),
        new RuntimeSetPrecision('16'),
        new RuntimeSetUriScheme('https'),
        new RuntimeSetTimeZone('UTC'),
    )
); // 3ms
            // ->addFile(App::FILEHANDLE_CONFIG)
// $Stopwatch->stop();
// dd($Stopwatch->records());
