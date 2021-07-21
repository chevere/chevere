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
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\DataStructure\MapInterface;
use Chevere\Interfaces\Dependent\DependentInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Workflow\StepInterface;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;
use Chevere\Interfaces\Workflow\WorkflowRunnerInterface;
use Throwable;

final class WorkflowRunner implements WorkflowRunnerInterface
{
    public function __construct(
        private WorkflowRunInterface $workflowRun
    ) {
    }

    public function workflowRun(): WorkflowRunInterface
    {
        return $this->workflowRun;
    }

    public function withRun(MapInterface $serviceContainer): static
    {
        $this->assertDependencies($serviceContainer);
        $new = clone $this;
        foreach ($new->workflowRun->workflow()->getGenerator() as $name => $step) {
            if ($new->workflowRun->has($name)) {
                continue;
            }

            try {
                $actionName = $step->action();
                /** @var ActionInterface $action */
                $action = new $actionName();
                $new->injectDependencies($action, $serviceContainer);
                $arguments = $new->getActionArguments($action, $step);
                $response = $new->getActionRunResponse($action, $arguments);
                $new->addStep($name, $step, $response);
            }
            // @codeCoverageIgnoreStart
            catch (Throwable $e) {
                throw new RuntimeException(
                    previous: $e,
                    message: (new Message('Step: %step% Action: %action%'))
                        ->code('%step%', $name)
                        ->code('%action%', $actionName)
                );
            }
            // @codeCoverageIgnoreEnd
        }

        return $new;
    }

    private function getActionRunResponse(ActionInterface $action, ArgumentsInterface $arguments): ResponseInterface
    {
        try {
            return $action->run($arguments);
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            $actionTrace = $e->getTrace()[1] ?? [];
            $fileLine = strtr('%file%:%line%', [
                '%file%' => $actionTrace['file'] ?? 'anon',
                '%line%' => $actionTrace['line'] ?? '0',
            ]);

            throw new InvalidArgumentException(
                previous: $e,
                message: (new Message('Missing argument(s) at %fileLine%'))
                    ->code('%fileLine%', $fileLine)
            );
        }
        // @codeCoverageIgnoreEnd
    }

    private function getActionArguments(ActionInterface $action, StepInterface $step): ArgumentsInterface
    {
        $arguments = $this->getStepArguments($step);

        try {
            return new Arguments($action->parameters(), ...$arguments);
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new InvalidArgumentException(
                previous: $e,
                message: (new Message('Missing argument(s)'))
            );
        }
        // @codeCoverageIgnoreEnd
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

    private function getStepArguments(StepInterface $step): array
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
                previous: $e,
                message: (new Message('Unmatched response from method %method%'))
                    ->code('%method%', $step->action() . '::run')
            );
        }
        // @codeCoverageIgnoreEnd
    }
}
