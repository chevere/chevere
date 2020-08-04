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
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\Parameters;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Workflow\TaskInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Ds\Map;
use Ds\Vector;
use Generator;
use Safe\Exceptions\PcreException;
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
        $this->name = (new Job($name))->toString();

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

    public function withAdded(string $step, TaskInterface $task): WorkflowInterface
    {
        $step = (new Job($step))->toString();
        $this->assertNoOverflow($step);
        $this->setParameters($step, $task);
        $new = clone $this;
        $new->map->put($step, $task);
        $new->steps->push($step);

        return $new;
    }

    public function withAddedBefore(string $before, string $step, TaskInterface $task): WorkflowInterface
    {
        $this->assertHasStepByName($before);
        $step = (new Job($step))->toString();
        $this->assertNoOverflow($step);
        $this->setParameters($step, $task);
        $new = clone $this;
        $new->map->put($step, $task);
        $new->steps->insert($new->getPosByName($before), $step);

        return $new;
    }

    public function withAddedAfter(string $after, string $step, TaskInterface $task): WorkflowInterface
    {
        $this->assertHasStepByName($after);
        $step = (new Job($step))->toString();
        $this->assertNoOverflow($step);
        $this->setParameters($step, $task);
        $new = clone $this;
        $new->map->put($step, $task);
        $new->steps->insert($new->getPosByName($after) + 1, $step);

        return $new;
    }

    public function has(string $step): bool
    {
        return $this->map->hasKey($step);
    }

    public function get(string $step): TaskInterface
    {
        try {
            return $this->map->get($step);
        }
        // @codeCoverageIgnoreStart
        catch (\OutOfBoundsException $e) {
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
            return $this->vars->get($variable);
        }
        // @codeCoverageIgnoreStart
        catch (\OverflowException $e) {
            throw new OverflowException(
                (new Message('Variable %variable% not found'))
                    ->code('%variable%', $variable)
            );
        }
        // @codeCoverageIgnoreEnd
    }

    public function getExpected(string $step): array
    {
        try {
            return $this->expected->get($step);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Step %step% not found'))
                    ->code('%step%', $step)
            );
        }
    }

    public function getGenerator(): Generator
    {
        foreach ($this->steps as $step) {
            yield $step => $this->get($step);
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
        foreach ($task->arguments() as $argument) {
            try {
                if (preg_match(self::REGEX_PARAMETER_REFERENCE, $argument, $matches)) {
                    $this->vars->put($argument, [$matches[1]]);
                    $this->putParameter(new Parameter($matches[1]));
                } elseif (preg_match(self::REGEX_STEP_REFERENCE, $argument, $matches)) {
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
        $this->parameters = $this->parameters->withAdded($parameter);
    }

    private function assertStepExists(string $step, array $matches): void
    {
        if (!$this->map->hasKey($matches[1])) {
            throw new InvalidArgumentException(
                (new Message("Step %step% references parameter %parameter% from previous step %prevStep% which doesn't exists"))
                    ->code('%step%', $step)
                    ->code('%parameter%', $matches[2])
                    ->code('%prevStep%', $matches[1])
            );
        }
    }
}
