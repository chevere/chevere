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

use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Bootstrap\Bootstrap;

require dirname(__DIR__) . '/vendor/autoload.php';

$documentRoot = rtrim(dirname(__DIR__, 'Chevereto-Chevere' == basename(__DIR__) ? 1 : 3), '/') . '/';
$isCli = 'cli' == php_sapi_name();

$bootstrap = (new Bootstrap($documentRoot))
  ->withCli($isCli);
$bootstrap = $bootstrap
  ->withConsole($bootstrap->isCli())
  ->withDev((bool) include($bootstrap->appPath() . 'options/dev.php'));

new BootstrapInstance($bootstrap);

// define('Chevere\DOCUMENT_ROOT', rtrim(dirname(__DIR__, 'Chevereto-Chevere' == basename(__DIR__) ? 1 : 3), '/') . '/');
// define('Chevere\ROOT_PATH', str_replace('\\', '/', DOCUMENT_ROOT));

require 'runtime.php';
require $bootstrap->appPath() . 'app.php';
require $bootstrap->appPath() . 'loader.php';
