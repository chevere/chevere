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

use Chevere\App\Builder;
use Chevere\Console\Container;
use Chevere\Runtime\Runtime;
use Chevere\Runtime\Sets\SetDebug;
use Chevere\Runtime\Sets\SetDefaultCharset;
use Chevere\Runtime\Sets\SetPrecision;
use Chevere\Runtime\Sets\SetTimeZone;
use Chevere\Runtime\Sets\SetUriScheme;
use Chevere\Runtime\Sets\SetLocale;
use Chevere\Runtime\Sets\SetErrorHandler;
use Chevere\Runtime\Sets\SetExceptionHandler;

define('BOOTSTRAP_TIME', microtime(true));

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

if ('cli' == php_sapi_name()) {
    new Container();
    define('Chevere\CLI', true);
} else {
    define('Chevere\CLI', false);
}

Builder::setRuntimeInstance(
    new Runtime(
        new SetDebug('1'),
        new SetErrorHandler('Chevere\ExceptionHandler\ErrorHandler::error'),
        new SetExceptionHandler('Chevere\ExceptionHandler\ExceptionHandler::exception'),
        new SetLocale('en_US.UTF8'),
        new SetDefaultCharset('utf-8'),
        new SetPrecision('16'),
        new SetUriScheme('https'),
        new SetTimeZone('UTC'),
        )
);

require APP_PATH . 'app.php';
require APP_PATH . 'loader.php';
