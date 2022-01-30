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

namespace Chevere\Action;

use Chevere\Action\Interfaces\ActionExecutedInterface;
use Chevere\Action\Interfaces\ActionRunnerInterface;
use Chevere\Controller\Interfaces\ControllerInterface;
use Throwable;

final class ActionRunner implements ActionRunnerInterface
{
    public function __construct(
        private ControllerInterface $controller
    ) {
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
