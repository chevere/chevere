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
use Chevere\Components\App\Instances\ScreenContainerInstance;
use Chevere\Components\Bootstrap\Bootstrap;
use Chevere\Components\Screen\Container;
use Chevere\Components\Screen\Screen;
use Chevere\Components\Screen\SilentScreen;

require 'vendor/autoload.php';

new BootstrapInstance(
    (new Bootstrap(__DIR__ . '/Chevere/TestApp/'))
        ->withCli(true)
        ->withDev(false)
        ->withAppAutoloader('Chevere\\TestApp\\App')
);

new ScreenContainerInstance(
    new Container(
        new Screen,
        new SilentScreen
    )
);
