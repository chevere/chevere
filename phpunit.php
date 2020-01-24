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
use Chevere\Components\Screen\ConsoleScreen;
use Chevere\Components\Screen\Container;
use Chevere\Components\Screen\DebugScreen;
use Chevere\Components\Screen\Interfaces\ContainerInterface;
use Chevere\Components\Screen\RuntimeScreen;
use Chevere\Components\Screen\Screen;
use Chevere\Components\Screen\ScreenContainer;
use Chevere\Components\Screen\SilentScreen;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

require 'vendor/autoload.php';

new BootstrapInstance(
    (new Bootstrap(__DIR__ . '/Chevere/TestApp/'))
        ->withCli(true)
        ->withDev(false)
        ->withAppAutoloader('Chevere\\TestApp\\App')
);

new ScreenContainerInstance(
    new ScreenContainer(
        (new Container(new RuntimeScreen(true)))
            ->withAddedScreen(
                ContainerInterface::DEBUG,
                new DebugScreen(true),
            )
            ->withAddedScreen(
                ContainerInterface::CONSOLE,
                new ConsoleScreen(true),
            )
    )
);

register_shutdown_function(function () {
    foreach (ScreenContainerInstance::get()->getAll() as $screen) {
        xdump($screen->trace());
    }
});
