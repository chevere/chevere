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
    new Console();
    define('Chevere\CLI', true);
} else {
    define('Chevere\CLI', false);
}

Loader::setDefaultRuntime(
    new Runtime(
        new RuntimeSetDebug('1'),
        // new RuntimeSetErrorHandler('Chevere\ExceptionHandler\ErrorHandler::error'),
        // new RuntimeSetExceptionHandler('Chevere\ExceptionHandler\ExceptionHandler::exception'),
        new RuntimeSetLocale('en_US.UTF8'),
        new RuntimeSetDefaultCharset('utf-8'),
        new RuntimeSetPrecision('16'),
        new RuntimeSetUriScheme('https'),
        new RuntimeSetTimeZone('UTC')
    )
);

require APP_PATH . 'app.php';
require APP_PATH . 'loader.php';
