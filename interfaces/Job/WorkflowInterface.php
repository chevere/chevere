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
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Core\UnexpectedValueException;
use Countable;
use Generator;

/**
 * Describes the component in charge of defining a collection of chained tasks.
 */
interface WorkflowInterface extends Countable
{
    const REGEX_VARIABLE = '/^\${([\w-]*)\:([\w-]*)}$/';

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
     *
     * @throws OverflowException
     */
    public function withAdded(string $name, TaskInterface $task): WorkflowInterface;

    /**
     * Return an instance with the specified `$task` added before `$before`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$task` added before `$before`.
     *
     * @throws OverflowException
     */
    public function withAddedBefore(string $before, string $name, TaskInterface $task): WorkflowInterface;

    /**
     * Return an instance with the specified `$task` added after `$after`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$task` added after `$after`.
     *
     * @throws OverflowException
     */
    public function withAddedAfter(string $after, string $name, TaskInterface $task): WorkflowInterface;

    public function get(string $taskName): TaskInterface;

    public function getParameters(string $taskName): array;

    public function keys(): array;
}
