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
use Chevere\Action\Interfaces\ActionInterface;
use Throwable;

/**
 * Runs the `$action` with the given `$namedArguments`.
 */
function runAction(
    ActionInterface $action,
    mixed ...$namedArguments
): ActionExecutedInterface {
    try {
        $runArguments = $action->getArguments(...$namedArguments);
        $response = $action->run($runArguments);
    } catch (Throwable $e) {
        return (new ActionExecuted([]))
            ->withThrowable($e, 1);
    }

    return new ActionExecuted($response->data());
}
