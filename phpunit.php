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
use Chevere\Components\Instances\WritersInstance;
use Chevere\Components\Bootstrap\Bootstrap;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Writers\Writers;

require 'vendor/autoload.php';

$rootDir = new Dir(new Path(__DIR__ . '/Chevere/TestApp'));

new BootstrapInstance(
    (new Bootstrap($rootDir, $rootDir->getChild('app')))
        ->withCli(true)
        ->withDev(false)
);

new WritersInstance(new Writers());
// new HooksInstance(
//     new Hooks(include 'hooks_classmap.php')
// );
