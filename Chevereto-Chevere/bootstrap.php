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
use Chevere\Console\Console;
use Chevere\Runtime\Runtime;
use Chevere\Runtime\Config;

define(__NAMESPACE__.'\TIME_BOOTSTRAP', microtime(true));
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

// Init console if sapi = cli
if ('cli' == php_sapi_name()) {
    Console::init();
}

const DEFAULT_ERROR_HANDLING = [
    Config::DEBUG => 1,
    Config::ERROR_HANDLER => 'Chevere\ErrorHandler\ErrorHandler::error',
    Config::EXCEPTION_HANDLER => 'Chevere\ErrorHandler\ErrorHandler::exception',
];

/*
 * Default error and exception handler
 */
new Runtime(
    (new Config())
        ->processFromArray(DEFAULT_ERROR_HANDLING)
);

define('Chevere\CLI', Console::isRunning());

App::setDefaultRuntime(
    new Runtime(
        (new Config())
            ->addArray([
                Config::LOCALE => 'en_US.UTF8',
                Config::DEFAULT_CHARSET => 'utf-8',
                Config::TIMEZONE => 'UTC',
                Config::URI_SCHEME => 'https',
            ] + DEFAULT_ERROR_HANDLING)
            ->addFile(App::FILEHANDLE_CONFIG)
            ->process()
    )
);
