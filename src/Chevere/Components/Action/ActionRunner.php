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

namespace Chevere\Components\Action;

use Chevere\Interfaces\Action\ActionExecutedInterface;
use Chevere\Interfaces\Action\ActionRunnerInterface;
use Chevere\Interfaces\Action\ControllerInterface;
use Throwable;

final class ActionRunner implements ActionRunnerInterface
{
    private ControllerInterface $controller;

    public function __construct(ControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    public function execute(mixed ...$arguments): ActionExecutedInterface
    {
        try {
            $response = $this->controller->run(
                $this->controller->getArguments(...$arguments)
            );
        } catch (Throwable $e) {
            return (new ActionExecuted([]))->withThrowable($e, 1);
        }

        return new ActionExecuted($response->data());
    }
}
