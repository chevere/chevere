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

namespace Chevere\Components\Controller;

use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\ControllerRanInterface;
use Throwable;

final class ControllerRunner
{
    private ControllerInterface $controller;

    public function __construct(ControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerRanInterface
    {
        try {
            $this->controller->setUp();
            $response = $this->controller->run($arguments);
            $this->controller->tearDown();
        } catch (Throwable $e) {
            return (new ControllerRan)->withThrowable($e);
        }
        $data = $response->data();

        return new ControllerRan($data);
    }
}
