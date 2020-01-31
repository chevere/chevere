<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere;

use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Bootstrap\Bootstrap;
use Chevere\Components\Console\Console;
use Chevere\Components\Filesystem\Dir\Dir;
use Chevere\Components\Filesystem\Path\Path;

require 'vendor/autoload.php';

$rootDir = new Dir(new Path(__DIR__));
$isCli = php_sapi_name() === 'cli';

$bootstrap = (new Bootstrap($rootDir, $rootDir->getChild('app')))
    ->withCli($isCli)
    ->withDev((bool) include($bootstrap->appPath() . 'options/dev.php'));

if ($isCli) {
    $bootstrap = $bootstrap
        ->withConsole(new Console);
}

new BootstrapInstance($bootstrap);

// define('Chevere\DOCUMENT_ROOT', rtrim(dirname(__DIR__, 'Chevereto-Chevere' == basename(__DIR__) ? 1 : 3), '/') . '/');
// define('Chevere\ROOT_PATH', str_replace('\\', '/', DOCUMENT_ROOT));

require 'runtime.php';
require $bootstrap->appDir() . 'app.php';
require $bootstrap->appDir() . 'loader.php';
