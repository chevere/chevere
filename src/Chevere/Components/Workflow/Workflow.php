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
use Chevere\Components\Parameter\Parameters;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Workflow\StepNameInterface;
use Chevere\Interfaces\Workflow\StepInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Ds\Map;
use Ds\Vector;
use Generator;
use Safe\Exceptions\PcreException;
use TypeError;
use function Safe\preg_match;

final class Workflow implements WorkflowInterface
{
    private string $name;

    private Map $map;

    private Vector $steps;

    private ParametersInterface $parameters;

    private Map $vars;

    private Map $expected;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->map = new Map;
        $this->steps = new Vector;
        $this->parameters = new Parameters;
        $this->vars = new Map;
        $this->expected = new Map;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function count(): int
    {
        return $this->steps->count();
    }    

    public function withAdded(StepInterface ...$task): WorkflowInterface
    {
        $new = clone $this;
        foreach($task as $taskArg) {
            $new->assertNoOverflow($taskArg);
            $new->setParameters($taskArg);
            $new->map->put($taskArg->name(), $taskArg);
            $new->steps->push($taskArg->name());
        }
        
        return $new;
    }

    public function withAddedBefore(string $before, StepInterface $task): WorkflowInterface
    {
        $new = clone $this;
        $new->assertHasStepByName($before);
        $new->assertNoOverflow($task);
        $new->setParameters($task);
        $new->map->put($task->name(), $task);
        $new->steps->insert($new->getPosByName($before), $task->name());

        return $new;
    }

    public function withAddedAfter(string $after, StepInterface $task): WorkflowInterface
    {
        $new = clone $this;
        $new->assertHasStepByName($after);
        $this->assertNoOverflow($task);
        $new->setParameters($task);
        $new->map->put($task->name(), $task);
        $new->steps->insert($new->getPosByName($after) + 1, $task->name());

        return $new;
    }

    public function has(string $step): bool
    {
        return $this->map->hasKey($step);
    }

    /**
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function get(string $step): StepInterface
    {
        try {
            return $this->map->get($step);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(new Message($e->getMessage()));
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Task %name% not found'))
                    ->code('%name%', $step)
            );
        }
    }

    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    public function order(): array
    {
        return $this->steps->toArray();
    }

    public function hasVar(string $variable): bool
    {
        return $this->vars->hasKey($variable);
    }

    /**
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function getVar(string $variable): array
    {
        try {
            return $this->vars->get($variable);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(new Message($e->getMessage()));
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Variable %variable% not found'))
                    ->code('%variable%', $variable)
            );
        }
    }

    /**
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function getExpected(StepNameInterface $step): array
    {
        try {
            return $this->expected->get($step->toString());
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(new Message($e->getMessage()));
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Step %step% not found'))
                    ->code('%step%', $step->toString())
            );
        }
    }

    public function getGenerator(): Generator
    {
        foreach ($this->steps as $step) {
            yield $step => $this->get($step);
        }
    }

    private function assertNoOverflow(StepInterface $task): void
    {
        if ($this->map->hasKey($task->name())) {
            throw new OverflowException(
                (new Message('Step name %name% has been already added.'))
                    ->code('%name%', $task->name())
            );
        }
    }

    private function setParameters(StepInterface $task): void
    {
        $action = $task->action();
        /**
         * @var ActionInterface $action
         */
        $action = new $action;
        $parameters = $action->parameters();
        foreach ($task->arguments() as $argument) {
            try {
                if (preg_match(self::REGEX_PARAMETER_REFERENCE, (string) $argument, $matches)) {
                    $this->vars->put($argument, [$matches[1]]);
                    $this->putParameter($parameters->get($matches[1]));
                } elseif (preg_match(self::REGEX_STEP_REFERENCE, (string) $argument, $matches)) {
                    $this->assertStepExists($task->name(), $matches);
                    $expected = $this->expected->get($matches[1], []);
                    $expected[] = $matches[2];
                    $this->expected->put($matches[1], $expected);
                    $this->vars->put($argument, [$matches[1], $matches[2]]);
                }
            }
            // @codeCoverageIgnoreStart
            catch (PcreException $e) {
                throw new LogicException(
                    (new Message('Invalid regex expression provided %regex%'))
                        ->code('%regex%', self::REGEX_STEP_REFERENCE)
                );
            }
            // @codeCoverageIgnoreEnd
        }
    }

    private function assertHasStepByName(string $step): void
    {
        if (!$this->map->hasKey($step)) {
            throw new OutOfBoundsException(
                (new Message("Task name %name% doesn't exists"))
                    ->code('%name%', $step)
            );
        }
    }

    private function getPosByName(string $step): int
    {
        $pos = $this->steps->find($step);
        /** @var int $pos */
        return $pos;
    }

    private function putParameter(ParameterInterface $parameter): void
    {
        if ($this->parameters->has($parameter->name())) {
            $this->parameters = $this->parameters->withModify($parameter);

            return;
        }
        $this->parameters = $this->parameters->withAddedRequired($parameter);
    }

    private function assertStepExists(string $step, array $matches): void
    {
        if (!$this->map->hasKey($matches[1])) {
            throw new OutOfBoundsException(
                (new Message("Step %step% references parameter %parameter% from previous step %prevStep% which doesn't exists"))
                    ->code('%step%', $step)
                    ->code('%parameter%', $matches[2])
                    ->code('%prevStep%', $matches[1])
            );
        }
    }
}
