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

use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\Interfaces\ActionRunInterface;
use Throwable;

/**
 * Runs the `$action` with the given `...$namedArguments`.
 */
function actionRun(
    ActionInterface $action,
    mixed ...$namedArguments
): ActionRunInterface {
    $runArguments = $action->getArguments(...$namedArguments);

    try {
        $response = $action->runner(...$runArguments->toArray());
    } catch (Throwable $e) {
        return (new ActionRun([]))
            ->withThrowable($e, 1);
    }

    return new ActionRun($response->data());
}
