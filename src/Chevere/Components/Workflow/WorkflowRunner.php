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
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;
use Chevere\Interfaces\Service\ServiceDependantInterface;
use Chevere\Interfaces\Workflow\StepInterface;
use Chevere\Interfaces\Workflow\TaskInterface;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;
use Chevere\Interfaces\Workflow\WorkflowRunnerInterface;
use Throwable;

final class WorkflowRunner implements WorkflowRunnerInterface
{
    private WorkflowRunInterface $workflowRun;

    private StepInterface $step;

    private TaskInterface $task;

    private string $actionName;

    private ActionInterface $action;

    private ResponseSuccessInterface $responseSuccess;

    public function __construct(WorkflowRunInterface $workflowRun)
    {
        $this->workflowRun = $workflowRun;
    }

    public function workflowRun(): WorkflowRunInterface
    {
        return $this->workflowRun;
    }

    public function run($container): WorkflowRunInterface
    {
        /**
         * @var string $step
         * @var TaskInterface $task
         */
        foreach ($this->workflowRun->workflow()->getGenerator() as $step => $task) {
            if ($this->workflowRun->has($step)) {
                continue; // @codeCoverageIgnore
            }
            $this->step = new Step($step);
            $this->task = $task;
            $this->actionName = $this->task->action();
            $this->action = new $this->actionName;
            $this->injectDependencies($container);
            // try {
            $this->responseSuccess = $this->action->run(
                $this->getArguments()
            );
            // }
            // @codeCoverageIgnoreStart
            // catch (Throwable $e) {
            //     throw new LogicException(new Message($e->getMessage() . 'eee'), 100);
            // }
            // @codeCoverageIgnoreEnd
            $this->addStep();
        }
        unset($this->step, $this->task, $this->actionName, $this->action, $this->responseSuccess);

        return $this->workflowRun;
    }

    private function injectDependencies($container): void
    {
        if ($this->action instanceof ServiceDependantInterface) {
            // Inject the services required by the action
        }
    }

    private function getArguments(): array
    {
        $arguments = [];
        foreach ($this->task->arguments() as $name => $taskArgument) {
            if (!$this->workflowRun->workflow()->hasVar($taskArgument)) {
                // @codeCoverageIgnoreStart
                $arguments[$name] = $taskArgument;
                continue;
                // @codeCoverageIgnoreEnd
            }
            $reference = $this->workflowRun->workflow()->getVar($taskArgument);
            if (isset($reference[1])) {
                $arguments[$name] = $this->workflowRun
                    ->get($reference[0])->data()[$reference[1]];
            } else {
                $arguments[$name] = $this->workflowRun
                    ->arguments()->get($reference[0]);
            }
        }

        return $arguments;
    }

    private function addStep(): void
    {
        try {
            $this->workflowRun = $this->workflowRun->withAdded(
                $this->step,
                $this->responseSuccess
            );
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new LogicException(
                (new Message('Unexpected response from method %method% at step %step%: %message%'))
                    ->code('%method%', $this->actionName . '::run')
                    ->code('%step%', $this->step->toString())
                    ->code('%message%', $e->getMessage())
            );
        }
        // @codeCoverageIgnoreEnd
    }
}
