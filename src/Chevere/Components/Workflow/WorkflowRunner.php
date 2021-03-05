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
use Chevere\Interfaces\DataStructure\MapInterface;
use Chevere\Interfaces\Dependent\DependentInterface;
use Chevere\Interfaces\Response\ResponseInterface;
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

    public function run(MapInterface $serviceContainer): WorkflowRunInterface
    {
        $this->assertDependencies($serviceContainer);
        foreach ($this->workflowRun->workflow()->getGenerator() as $name => $step) {
            if ($this->workflowRun->has($name)) {
                // @codeCoverageIgnore
                continue;
            }
            $actionName = $step->action();
            /** @var ActionInterface $action */
            $action = new $actionName();
            $this->injectDependencies($action, $serviceContainer);
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

    private function assertDependencies(MapInterface $serviceContainer): void
    {
        $dependencies = $this->workflowRun->workflow()->dependencies();
        $missing = [];
        foreach ($dependencies->getGenerator() as $name => $className) {
            $isMissing =
                ! $serviceContainer->has($name) ||
                ! is_a($serviceContainer->get($name), $className, false);
            if ($isMissing) {
                $missing[] = "${name}:${className}";

                continue;
            }
        }
        if ($missing !== []) {
            throw new LogicException(
                message: (new Message('Missing %missing% dependency(ies)'))
                    ->code('%missing%', implode(', ', $missing))
            );
        }
    }

    private function injectDependencies(ActionInterface &$action, MapInterface $serviceContainer): void
    {
        if ($action instanceof DependentInterface) {
            $instances = [];
            foreach ($action->dependencies()->getGenerator() as $name => $className) {
                $instances[$name] = $serviceContainer->get($name);
            }
            $action = $action->withDependencies(...$instances);
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

    private function addStep(string $name, StepInterface $step, ResponseInterface $response): void
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
