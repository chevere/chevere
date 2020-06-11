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
use Chevere\Components\Instances\WritersInstance;
use Chevere\Components\Plugs\EventListeners\EventListenersQueue;
use Chevere\Components\Plugs\EventListeners\EventListenersRunner;
use Chevere\Components\Writers\Writers;
use Chevere\Examples\EventHelloWorldController;
use Chevere\Examples\HelloWorldEvent;
use Chevere\Interfaces\Controller\ControllerInterface;

require 'vendor/autoload.php';
new WritersInstance(new Writers);
$controller = new EventHelloWorldController;
$controller = $controller->withEventListenersRunner(
    new EventListenersRunner(
        (new EventListenersQueue)
            ->withAddedEventListener(new HelloWorldEvent)
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
echo "\n-----\n";
echo implode(' ', $ran->data());

// event:greetSet Hello, World
// -----
// Hello, World
