<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere;

use Chevere\Components\App\Builder;
use Chevere\Components\Console\Console;
use Chevere\Components\Console\Terminal;
use Chevere\Components\Http\RequestContainer;
use Chevere\Components\Http\ServerRequest;
use Chevere\Components\Runtime\Runtime;
use Chevere\Components\Runtime\Sets\SetDebug;
use Chevere\Components\Runtime\Sets\SetDefaultCharset;
use Chevere\Components\Runtime\Sets\SetErrorHandler;
use Chevere\Components\Runtime\Sets\SetExceptionHandler;
use Chevere\Components\Runtime\Sets\SetLocale;
use Chevere\Components\Runtime\Sets\SetPrecision;
use Chevere\Components\Runtime\Sets\SetTimeZone;
use Chevere\Components\Runtime\Sets\SetUriScheme;

define('BOOTSTRAP_TIME', (int) hrtime(true));

require dirname(__DIR__) . '/vendor/autoload.php';

define('Chevere\DOCUMENT_ROOT', dirname(__DIR__, basename(__DIR__) == 'Chevereto-Chevere' ? 1 : 3));

/* Root path containing /app */
define('Chevere\ROOT_PATH', rtrim(str_replace('\\', '/', DOCUMENT_ROOT), '/') . '/');

/*
 * Chevere\PATH
 * Relative path to Core, 'vendor/chevereto/chevere'
 */
// define('Chevere\PATH', ROOT_PATH . 'vendor/chevereto/chevere/');

define('Chevere\APP_PATH', ROOT_PATH . 'app/');

/** DEV=true to rebuild the App on every load */
define('Chevere\DEV', (bool) include(APP_PATH . 'options/dev.php'));

new RequestContainer(
    ServerRequest::fromGlobals()
);

if ('cli' == php_sapi_name()) {
    define('Chevere\CLI', true);
    new Terminal(new Console());
} else {
    define('Chevere\CLI', false);
}

Builder::setRuntimeInstance(
    new Runtime(
        new SetDebug('1'),
        new SetErrorHandler('Chevere\Components\ExceptionHandler\ErrorHandler::error'),
        new SetExceptionHandler('Chevere\Components\ExceptionHandler\ExceptionHandler::exception'),
        new SetLocale('en_US.UTF8'),
        new SetDefaultCharset('utf-8'),
        new SetPrecision('16'),
        new SetUriScheme('https'),
        new SetTimeZone('UTC'),
    )
);

require APP_PATH . 'app.php';
require APP_PATH . 'loader.php';
