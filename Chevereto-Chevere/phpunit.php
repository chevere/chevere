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

use function ChevereFn\stringReplaceFirst;
use function ChevereFn\stringStartsWith;

define('Chevere\BOOTSTRAP_TIME', (int) hrtime(true));
require dirname(__DIR__) . '/vendor/autoload.php';
define('Chevere\DOCUMENT_ROOT', __DIR__ . '/Chevere/TestApp/');
define('Chevere\ROOT_PATH', rtrim(str_replace('\\', '/', DOCUMENT_ROOT), '/') . '/');
define('Chevere\APP_PATH', ROOT_PATH . 'app/');

define('Chevere\CLI', true);
define('Chevere\DEV', false);

spl_autoload_register(function ($className) {
    $isTestApp = stringStartsWith('Chevere\\TestApp\\App\\', $className);
    if ($isTestApp) {
        $name = str_replace('\\', '/', $className);
        $path = stringReplaceFirst('Chevere/TestApp/App/', '', $name) . '.php';
        require DOCUMENT_ROOT . 'app/src/' . $path;
    }
});
