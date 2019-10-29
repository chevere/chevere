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

use Chevere\Components\Path\Path;

define('Chevere\BOOTSTRAP_TIME', (int) hrtime(true));
require dirname(__DIR__) . '/vendor/autoload.php';
define('Chevere\DOCUMENT_ROOT', rtrim(dirname(__DIR__, basename(__DIR__) == 'Chevereto-Chevere' ? 1 : 3), '/') . '/');
define('Chevere\ROOT_PATH', str_replace('\\', '/', DOCUMENT_ROOT));
define('Chevere\APP_PATH', ROOT_PATH . 'app/');

// FIXME: Create a container for runtime booleans
define('Chevere\CLI', 'cli' == php_sapi_name());
define('Chevere\DEV', (bool) include(APP_PATH . 'options/dev.php')); // DEV=true to rebuild the App on every load

require 'runtime.php';
require APP_PATH . 'app.php';
require APP_PATH . 'loader.php';
