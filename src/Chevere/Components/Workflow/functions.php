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

namespace Chevere\Components\Workflow;

use Chevere\Interfaces\Workflow\StepInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Chevere\Interfaces\Workflow\WorkflowMessageInterface;

/**
 * @codeCoverageIgnore
 */
function getWorkflowMessage(WorkflowInterface $workflow, mixed ...$namedArguments): WorkflowMessageInterface
{
    return new WorkflowMessage(new WorkflowRun($workflow, ...$namedArguments));
}

/**
 * Push `$workflowQueue` to the queue
 * @codeCoverageIgnore
 */
function pushWorkflowQueue(WorkflowMessageInterface $workflowMessage, $stack): void
{
    $stack->push($workflowMessage);
    // if (in_array($uuid, $stack)) {
    //     throw new OutOfBoundsException(
    //         (new Message('Queue uuid %uuid% already exists'))
    //             ->code('%uuid%', $uuid)
    //     );
    // }
    $stack[] = $workflowMessage;
}

// @codeCoverageIgnoreStart

function workflow(StepInterface ...$steps) : WorkflowInterface {

    return new Workflow(...$steps);
}

function step(string $action, mixed ...$namedArguments): StepInterface
{
    return new Step($action, ...$namedArguments);
}