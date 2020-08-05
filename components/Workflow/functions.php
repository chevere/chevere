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
use Chevere\Interfaces\Workflow\ActionInterface;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;

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
        $magicArguments = $task->arguments();
        $arguments = [];
        foreach ($magicArguments as $name => $magicArgument) {
            if (!$workflowRun->workflow()->hasVar($magicArgument)) {
                // @codeCoverageIgnoreStart
                $arguments[$name] = $magicArgument;
                continue;
                // @codeCoverageIgnoreEnd
            }
            $reference = $workflowRun->workflow()->getVar($magicArgument);
            if (isset($reference[1])) {
                $arguments[$name] = $workflowRun->get($reference[0])->data()[$reference[1]];
            } else {
                $arguments[$name] = $workflowRun->arguments()->get($reference[0]);
            }
        }
        $actionArguments = new Arguments($action->getParameters(), $arguments);
        $response = $action->run($actionArguments);
        try {
            $workflowRun = $workflowRun->withAdded($step, $response);
        }
        // @codeCoverageIgnoreStart
        catch (ArgumentCountException $e) {
            throw new LogicException(
                (new Message('Unexpected response from method %method% at step %step%'))
                    ->code('%method%', $actionName . '::run')
                    ->code('%step%', $step),
                0,
                $e
            );
        }
        // @codeCoverageIgnoreEnd
    }

    return $workflowRun;
}
