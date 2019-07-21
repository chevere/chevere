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

namespace Chevereto\Chevere;

use Chevereto\Chevere\App\App;
use Chevereto\Chevere\Runtime\Runtime;
use Chevereto\Chevere\Runtime\Config;

define(__NAMESPACE__.'\TIME_BOOTSTRAP', microtime(true));
define(__NAMESPACE__.'\ERROR_LEVEL_BOOTSTRAP', error_reporting());

// Namespace handles (adds trailing slashes)
const CORE_NS_HANDLE = __NAMESPACE__.'\\';
const APP_NS_HANDLE = 'App\\';

/*
 * Assuming that this file has been loaded from /app/bootstrap.php
 */
define('Chevereto\Chevere\BOOTSTRAPPER', debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file']);

/*
 * Chevereto\Chevere\ROOT_PATH
 * Root path containing /app
 */
define('Chevereto\Chevere\ROOT_PATH', rtrim(str_replace('\\', '/', dirname(BOOTSTRAPPER, 2)), '/').'/');

/*
 * Chevereto\Chevere\PATH
 * Relative path to Chevereto\Core, usually 'vendor/chevereto/chevereto-core'
 */
define('Chevereto\Chevere\PATH', rtrim(str_replace(ROOT_PATH, null, str_replace('\\', '/', __DIR__)), '/').'/');

/*
 * Chevereto\Chevere\App\PATH
 * Relative path to app, usually 'app'
 */
define('Chevereto\Chevere\App\PATH', basename(dirname(BOOTSTRAPPER)).'/');

// Init console if sapi = cli
if ('cli' == php_sapi_name()) {
    Console::init();
}

const DEFAULT_ERROR_HANDLING = [
    Config::DEBUG => 1,
    Config::ERROR_HANDLER => 'Chevereto\Chevere\ErrorHandler\ErrorHandler::error',
    Config::EXCEPTION_HANDLER => 'Chevereto\Chevere\ErrorHandler\ErrorHandler::exception',
];

/*
 * Default error and exception handler
 */
// new Runtime(
//     (new Config())
//         ->processFromArray(DEFAULT_ERROR_HANDLING)
// );

// This constant allows safe short syntax like `CLI && Console::io()` in all namespaces.
define('Chevereto\Chevere\CLI', Console::isRunning());

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
