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

use Chevere\Components\Bootstrap\Bootstrap;
use Chevere\Components\Console\Console;
use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Instances\BootstrapInstance;

require 'vendor/autoload.php';

$rootDir = new DirFromString(__DIR__ . '/build');
$isCli = php_sapi_name() === 'cli';

$bootstrap = (new Bootstrap($rootDir, $rootDir))
    ->withCli($isCli);

new BootstrapInstance($bootstrap);

require 'runtime.php';
require $bootstrap->appDir()->path()->absolute() . 'app.php';
require $bootstrap->appDir()->path()->absolute() . 'loader.php';
