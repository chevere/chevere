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
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Workflow\TaskInterface;
use Chevere\Interfaces\Workflow\WorkflowInterface;
use Ds\Map;
use Ds\Vector;
use Safe\Exceptions\PcreException;
use function DeepCopy\deep_copy;
use function Safe\preg_match;

final class Workflow implements WorkflowInterface
{
    private string $name;

    private Map $map;

    private Vector $steps;

    private ParametersInterface $parameters;

    private Map $references;

    private Map $expected;

    public function __construct(string $name)
    {
        $this->name = (new Job($name))->toString();

        $this->map = new Map;
        $this->steps = new Vector;
        $this->parameters = new Parameters;
        $this->references = new Map;
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

    public function withAdded(string $name, TaskInterface $task): WorkflowInterface
    {
        $name = (new Job($name))->toString();
        $this->assertNoOverflow($name);
        $this->setParameters($name, $task);
        $new = clone $this;
        $new->map->put($name, $task);
        $new->steps->push($name);

        return $new;
    }

    public function withAddedBefore(string $before, string $name, TaskInterface $task): WorkflowInterface
    {
        $this->assertHasTaskByName($before);
        $name = (new Job($name))->toString();
        $this->assertNoOverflow($name);
        $this->setParameters($name, $task);
        $new = clone $this;
        $new->map->put($name, $task);
        $new->steps->insert($new->getPosByName($before), $name);

        return $new;
    }

    public function withAddedAfter(string $after, string $name, TaskInterface $task): WorkflowInterface
    {
        $this->assertHasTaskByName($after);
        $name = (new Job($name))->toString();
        $this->assertNoOverflow($name);
        $this->setParameters($name, $task);
        $new = clone $this;
        $new->map->put($name, $task);
        $new->steps->insert($new->getPosByName($after) + 1, $name);

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

    public function hasReference(string $reference): bool
    {
        return $this->references->hasKey($reference);
    }

    public function getReference(string $reference): array
    {
        try {
            return $this->references->get($reference);
        }
        // @codeCoverageIgnoreStart
        catch (\OverflowException $e) {
            throw new OverflowException(
                (new Message('Reference %reference% not found'))
                    ->code('%reference%', $reference)
            );
        }
        // @codeCoverageIgnoreEnd
    }

    public function getExpected(string $step): array
    {
        return $this->expected->get($step);
    }

    private function assertNoOverflow(string $name): void
    {
        if ($this->map->hasKey($name)) {
            throw new OverflowException(
                (new Message('Task name %name% has been already added.'))
                    ->code('%name%', $name)
            );
        }
    }

    private function setParameters(string $name, TaskInterface $task): void
    {
        /**
         * @var string $argument
         */
        foreach ($task->arguments() as $argument) {
            try {
                if (preg_match(self::REGEX_PARAMETER_REFERENCE, $argument, $matches)) {
                    $this->parameters = $this->parameters->withAdded(new Parameter($matches[1]));
                } elseif (preg_match(self::REGEX_STEP_REFERENCE, $argument, $matches)) {
                    if ($matches[1] !== 'job' && !$this->map->hasKey($matches[1])) {
                        throw new InvalidArgumentException(
                            (new Message("Task %name% references parameter %parameter% from task %task% which doesn't exists"))
                                ->code('%name%', $name)
                                ->code('%parameter%', $matches[2])
                                ->code('%task%', $matches[1])
                        );
                    }
                    $expected = $this->expected->get($matches[1], []);
                    $expected[] = $matches[2];
                    $this->expected->put($matches[1], $expected);
                    $this->references->put($argument, [$matches[1], $matches[2]]);
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

    private function assertHasTaskByName(string $name): void
    {
        if (!$this->map->hasKey($name)) {
            throw new OutOfBoundsException(
                (new Message("Task name %name% doesn't exists"))
                    ->code('%name%', $name)
            );
        }
    }

    private function getPosByName(string $name): int
    {
        $pos = $this->steps->find($name);
        /** @var int $pos */
        return $pos;
    }
}
