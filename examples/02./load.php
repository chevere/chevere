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

use Chevere\Components\Bootstrap\Bootstrap;
use Chevere\Components\Controller\ControllerArguments;
use Chevere\Components\Controller\ControllerRunner;
use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\Instances\WritersInstance;
use Chevere\Components\Plugin\PlugsQueue;
use Chevere\Components\Plugin\Types\HookPlugType;
use Chevere\Components\Plugs\Hooks\HooksQueue;
use Chevere\Components\Plugs\Hooks\HooksRunner;
use Chevere\Components\Writers\Writers;
use Chevere\Examples\HelloWorldController;
use Chevere\Interfaces\Controller\ControllerInterface;

require 'vendor/autoload.php';
new BootstrapInstance((new Bootstrap(new DirFromString(__DIR__ . '/'))));
new WritersInstance(new Writers);
$controller = new HelloWorldController;
$controller = $controller->withHooksRunner(
    new HooksRunner(
        new HooksQueue(
            (new PlugsQueue(new HookPlugType))
                ->withAddedPlug(new HelloWorldHook)
        )
    )
);
/**
 * @var ControllerInterface $controller
 */
$runner = new ControllerRunner($controller);
$arguments = new ControllerArguments(
    $controller->parameters(),
    ['name' => 'World']
);
$ran = $runner->ran($arguments);
echo implode(' ', $ran->data());
