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
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;
use Chevere\Interfaces\Service\ServiceDependantInterface;
use Chevere\Interfaces\Workflow\StepInterface;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;
use Chevere\Interfaces\Workflow\WorkflowRunnerInterface;
use Throwable;

final class WorkflowRunner implements WorkflowRunnerInterface
{
    private WorkflowRunInterface $workflowRun;

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
         * @var StepInterface $task
         */
        foreach ($this->workflowRun->workflow()->getGenerator() as $name => $step) {
            if ($this->workflowRun->has($name)) {
                // @codeCoverageIgnore
                continue;
            }
            $actionName = $step->action();
            /** @var ActionInterface $action */
            $action = new $actionName();
            $this->injectDependencies($action, $container);
            // try {
            $responseSuccess = $action->run(
                new Arguments($action->parameters(), ...$this->getArguments($step))
            );
            // }
            // @codeCoverageIgnoreStart
            // catch (Throwable $e) {
            //     throw new LogicException(new Message($e->getMessage() . 'eee'), 100);
            // }
            // @codeCoverageIgnoreEnd
            $this->addStep($name, $step, $responseSuccess);
        }

        return $this->workflowRun;
    }

    private function injectDependencies(ActionInterface $action, $container): void
    {
        if ($action instanceof ServiceDependantInterface) {
            // Inject the services required by the action
        }
    }

    private function getArguments(StepInterface $step): array
    {
        $arguments = [];
        foreach ($step->arguments() as $name => $taskArgument) {
            if (! $this->workflowRun->workflow()->hasVar($taskArgument)) {
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

    private function addStep(string $name, StepInterface $step, ResponseSuccessInterface $response): void
    {
        try {
            $this->workflowRun = $this->workflowRun->withStepResponse($name, $response);
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new LogicException(
                (new Message('Unexpected response from method %method% at step %step%: %message%'))
                    ->code('%method%', $step->action() . '::run')
                    ->code('%step%', $name)
                    ->code('%message%', $e->getMessage())
            );
        }
        // @codeCoverageIgnoreEnd
    }
}
