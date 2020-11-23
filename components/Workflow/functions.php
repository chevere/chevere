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

use Chevere\Components\Message\Message;
use Chevere\Components\Parameter\Arguments;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Response\ResponseFailureInterface;
use Chevere\Interfaces\Service\ServiceDependantInterface;
use Chevere\Interfaces\Workflow\WorkflowMessageInterface;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;

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

/**
 * Runs a WorkflowRun
 * @codeCoverageIgnore
 */
function workflowRunner(WorkflowRunInterface $workflowRun): WorkflowRunInterface
{
    foreach ($workflowRun->workflow()->getGenerator() as $step => $task) {
        if ($workflowRun->has($step)) {
            continue; // @codeCoverageIgnore
        }
        $actionName = $task->action();
        /**
         * @var ActionInterface $action
         */
        $action = new $actionName;
        if ($action instanceof ServiceDependantInterface) {
            // Inject the services required by the action
        }
        $arguments = [];
        foreach ($task->arguments() as $name => $taskArgument) {
            if (!$workflowRun->workflow()->hasVar($taskArgument)) {
                // @codeCoverageIgnoreStart
                $arguments[$name] = $taskArgument;
                continue;
                // @codeCoverageIgnoreEnd
            }
            $reference = $workflowRun->workflow()->getVar($taskArgument);
            if (isset($reference[1])) {
                $arguments[$name] = $workflowRun->get($reference[0])->data()[$reference[1]];
            } else {
                $arguments[$name] = $workflowRun->arguments()->get($reference[0]);
            }
        }
        $actionArguments = new Arguments($action->getParameters(), $arguments);
        $response = $action->run($actionArguments);
        // @codeCoverageIgnoreStart
        if ($response instanceof ResponseFailureInterface) {
            throw new LogicException(
                (new Message('Step %step% for workflow %workflow% replied with a response failure at %method%: %message%'))
                    ->code('%workflow%', $workflowRun->workflow()->name())
                    ->code('%step%', $step)
                    ->code('%method%', $actionName . '::run')
                    ->strtr('%message%', $response->data()['message'])
            );
        }
        // @codeCoverageIgnoreEnd
        try {
            $workflowRun = $workflowRun->withAdded($step, $response);
        } catch (ArgumentCountException $e) {
            throw new LogicException(
                (new Message('Unexpected response from method %method% at step %step%'))
                    ->code('%method%', $actionName . '::run')
                    ->code('%step%', $step),
                0,
                $e
            );
        }
    }

    return $workflowRun;
}
