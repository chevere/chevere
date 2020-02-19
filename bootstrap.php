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

use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\Bootstrap\Bootstrap;
use Chevere\Components\Console\Console;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;

require 'vendor/autoload.php';

$rootDir = new Dir(new Path(__DIR__ . '/build'));
$isCli = php_sapi_name() === 'cli';

$bootstrap = (new Bootstrap($rootDir, $rootDir->getChild('app')))
    ->withCli($isCli)
    ->withDev((bool) include($bootstrap->appPath() . 'options/dev.php'));

if ($isCli) {
    $bootstrap = $bootstrap->withConsole(new Console);
}

new BootstrapInstance($bootstrap);

require 'runtime.php';
require $bootstrap->appDir()->path()->absolute() . 'app.php';
require $bootstrap->appDir()->path()->absolute() . 'loader.php';
