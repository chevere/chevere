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

namespace Chevere\Interfaces\Job;

use Chevere\Exceptions\Core\InvalidArgumentException;

/**
 * Describes the component in charge of defining a job.
 */
interface JobInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $name);

    /**
     * Provides access to the instance id.
     */
    public function id(): string;

    /**
     * Provides access to the instance name.
     */
    public function name(): string;

    /**
     * Return an instance with the specified `$task`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$task`.
     */
    public function with(TaskInterface $task): JobInterface;

    /**
     * Return an instance with the specified `$task` added before `$before`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$task` added before `$before`.
     */
    public function withBefore(string $before, TaskInterface $task): JobInterface;

    /**
     * Return an instance with the specified `$task` added after `$after`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$task` added after `$after`.
     */
    public function withAfter(string $after, TaskInterface $task): JobInterface;
}
