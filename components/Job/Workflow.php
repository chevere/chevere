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

namespace Chevere\Components\Job;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Job\TaskInterface;
use Chevere\Interfaces\Job\WorkflowInterface;
use Ds\Map;
use Ds\Set;
use Ds\Vector;
use Safe\Exceptions\PcreException;
use function DeepCopy\deep_copy;
use function Safe\preg_match;

final class Workflow implements WorkflowInterface
{
    private string $id;

    private string $name;

    private Map $map;

    private Vector $tasks;

    private Map $arguments;

    public function __construct(string $name)
    {
        $this->name = (new Job($name))->toString();
        $this->id = uniqid('', true) . '@' . time();
        $this->map = new Map;
        $this->tasks = new Vector;
        $this->arguments = new Map(['job' => new Set]);
    }

    public function count(): int
    {
        return $this->tasks->count();
    }

    public function __clone()
    {
        $this->map = deep_copy($this->map);
        $this->tasks = deep_copy($this->tasks);
        $this->arguments = deep_copy($this->arguments);
    }

    public function id(): string
    {
        return $this->id;
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
        $new->tasks->push($name);

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
        $new->tasks->insert($new->getPosByName($before), $name);

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
        $new->tasks->insert($new->getPosByName($after) + 1, $name);

        return $new;
    }

    public function get(string $taskName): TaskInterface
    {
        try {
            return $this->map->get($taskName);
        }
        // @codeCoverageIgnoreStart
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Task %name% not found'))
                    ->code('%name%', $taskName)
            );
        }
        // @codeCoverageIgnoreEnd
    }

    public function getParameters(string $taskName): array
    {
        try {
            return $this->arguments->get($taskName)->toArray();
        }
        // @codeCoverageIgnoreStart
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Task %name% not found'))
                    ->code('%name%', $taskName)
            );
        }
        // @codeCoverageIgnoreEnd
    }

    public function keys(): array
    {
        return $this->tasks->toArray();
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
                $putArgument = $argument;
                if (preg_match(self::REGEX_VARIABLE, $argument, $matches)) {
                    if ($matches[1] !== 'job' && !$this->map->hasKey($matches[1])) {
                        throw new InvalidArgumentException(
                            (new Message("Task %name% references parameter %parameter% from task %task% which doesn't exists"))
                                ->code('%name%', $name)
                                ->code('%parameter%', $matches[2])
                                ->code('%task%', $matches[1])
                        );
                    } else {
                        $this->arguments->put('job', $this->getArguments('job', $matches[2]));
                    }
                    $putArgument = [$matches[1], $matches[2]];
                }
                $this->arguments->put($name, $this->getArguments($name, $putArgument));
            }
            // @codeCoverageIgnoreStart
            catch (PcreException $e) {
                throw new LogicException(
                    (new Message('Invalid regex expression provided %regex%'))
                        ->code('%regex%', self::REGEX_VARIABLE)
                );
            }
            // @codeCoverageIgnoreEnd
        }
    }

    private function getArguments(string $name, $argument): Set
    {
        $arguments = $this->arguments->get($name, new Set);
        if (!$arguments->contains($argument)) {
            $arguments->add($argument);
        }

        return $arguments;
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
        $pos = $this->tasks->find($name);
        /** @var int $pos */
        return $pos;
    }
}
