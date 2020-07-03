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

use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerExecutedInterface;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Controller\ControllerRunnerInterface;
use Throwable;

final class ControllerRunner implements ControllerRunnerInterface
{
    private ControllerInterface $controller;

    public function __construct(ControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    public function execute(ControllerArgumentsInterface $arguments): ControllerExecutedInterface
    {
        try {
            $this->controller->setUp();
            $response = $this->controller->run($arguments);
            $this->controller->tearDown();
        } catch (Throwable $e) {
            return (new ControllerExecuted([]))->withThrowable($e, 1);
        }
        $data = $response->data();

        return new ControllerExecuted($data);
    }
}
