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

use Chevere\Interfaces\Job\JobInterface;
use Chevere\Interfaces\Job\TaskInterface;
use Ds\Map;
use function DeepCopy\deep_copy;

final class Job implements JobInterface
{
    private string $id;

    private string $name;

    private Map $map;

    public function __construct(string $name)
    {
        $this->id = uniqid('', true) . '@' . time();
        $this->name = $name;
        $this->map = new Map;
    }

    public function __clone()
    {
        $this->map = deep_copy($this->map);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function with(TaskInterface $task): JobInterface
    {
        $new = clone $this;
        $new->map->put($task->name(), $task);

        return $new;
    }

    public function withBefore(string $before, TaskInterface $task): JobInterface
    {
        $new = clone $this;
        // add $task before $before
        return $new;
    }

    public function withAfter(string $after, TaskInterface $task): JobInterface
    {
        $new = clone $this;
        // add $task after $after
        return $new;
    }
}
