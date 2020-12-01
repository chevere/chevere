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
use Chevere\Interfaces\Workflow\StepInterface;
use Chevere\Interfaces\Workflow\TaskInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Ds\Map;
use Ds\Vector;
use Generator;
use Safe\Exceptions\PcreException;
use TypeError;
use function Chevere\Components\Type\returnTypeExceptionMessage;
use function DeepCopy\deep_copy;
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
        $this->name = (new Step($name))->toString();
        $this->map = new Map;
        $this->steps = new Vector;
        $this->parameters = new Parameters;
        $this->vars = new Map;
        $this->expected = new Map;
    }

    public function count(): int
    {
        return $this->steps->count();
    }

    public function __clone()
    {
        $this->map = deep_copy($this->map);
        $this->steps = deep_copy($this->steps);
        $this->parameters = deep_copy($this->parameters);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function withAdded(StepInterface $step, TaskInterface $task): WorkflowInterface
    {
        $step = $step->toString();
        $this->assertNoOverflow($step);
        $new = clone $this;
        $new->setParameters($step, $task);
        $new->map->put($step, $task);
        $new->steps->push($step);

        return $new;
    }

    public function withAddedBefore(StepInterface $before, StepInterface $step, TaskInterface $task): WorkflowInterface
    {
        $before = $before->toString();
        $step = $step->toString();
        $this->assertHasStepByName($before);
        $this->assertNoOverflow($step);
        $new = clone $this;
        $new->setParameters($step, $task);
        $new->map->put($step, $task);
        $new->steps->insert($new->getPosByName($before), $step);

        return $new;
    }

    public function withAddedAfter(StepInterface $after, StepInterface $step, TaskInterface $task): WorkflowInterface
    {
        $after = $after->toString();
        $step = $step->toString();
        $this->assertHasStepByName($after);
        $this->assertNoOverflow($step);
        $new = clone $this;
        $new->setParameters($step, $task);
        $new->map->put($step, $task);
        $new->steps->insert($new->getPosByName($after) + 1, $step);

        return $new;
    }

    public function has(StepInterface $step): bool
    {
        return $this->map->hasKey($step->toString());
    }

    public function get(StepInterface $step): TaskInterface
    {
        $step = $step->toString();
        try {
            /**
             * @var TaskInterface $return
             */
            $return = $this->map->get($step);

            return $return;
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(
                returnTypeExceptionMessage(TaskInterface::class, $return ?? null)
            );
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Task %name% not found'))
                    ->code('%name%', $step)
            );
        }
        // @codeCoverageIgnoreEnd
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

    public function getVar(string $variable): array
    {
        try {
            /** @var array $return */
            $return = $this->vars->get($variable);

            return $return;
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(
                returnTypeExceptionMessage('array', $return ?? null)
            );
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Variable %variable% not found'))
                    ->code('%variable%', $variable)
            );
        }
        // @codeCoverageIgnoreEnd
    }

    public function getExpected(StepInterface $step): array
    {
        try {
            /** @var array $return */
            $return = $this->expected->get($step->toString());

            return $return;
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(
                returnTypeExceptionMessage('array', $return ?? null)
            );
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
            yield $step => $this->get(new Step($step));
        }
    }

    private function assertNoOverflow(string $step): void
    {
        if ($this->map->hasKey($step)) {
            throw new OverflowException(
                (new Message('Step name %name% has been already added.'))
                    ->code('%name%', $step)
            );
        }
    }

    private function setParameters(string $step, TaskInterface $task): void
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
                    $this->assertStepExists($step, $matches);
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
