<?php

/*
 * This file is part of Chevereto.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
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
            $response = $this->controller->run($arguments);
        } catch (Throwable $e) {
            return (new ControllerExecuted([]))->withThrowable($e, 1);
        }

        return new ControllerExecuted($response->data());
    }
}
