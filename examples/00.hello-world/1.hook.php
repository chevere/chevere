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

use Chevere\Components\Controller\ControllerArguments;
use Chevere\Components\Controller\ControllerRunner;
use Chevere\Components\Plugs\Hooks\HooksQueue;
use Chevere\Components\Plugs\Hooks\HooksRunner;
use Chevere\Examples\HelloWorldHookHook;
use Chevere\Examples\HookHelloWorldController;
use Chevere\Interfaces\Controller\ControllerInterface;

require 'vendor/autoload.php';

$controller = new HookHelloWorldController;
$controller = $controller->withHooksRunner(
    new HooksRunner(
        (new HooksQueue)
            ->withAddedHook(new HelloWorldHookHook)
    )
);
/**
 * @var ControllerInterface $controller
 */
$arguments = new ControllerArguments(
    $controller->parameters(),
    ['name' => 'World']
);
$runner = new ControllerRunner($controller);
$ran = $runner->ran($arguments);
echo implode(' ', $ran->data());

// Hello, World!!
