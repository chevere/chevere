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

namespace Chevere\Interfaces\Attribute;

use Chevere\Interfaces\DataStructures\MapInterface;
use Generator;

/**
 * Describes the component in charge of collecting objects implementing `ConditionInterface`.
 */
interface ConditionsInterface extends MapInterface
{
    /**
     * Return an instance with the specified `$condition` added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$condition` added.
     *
     * @throws OverflowException
     */
    public function withAdded(ConditionInterface $condition): ConditionsInterface;

    /**
     * Return an instance with the specified `$condition` modifying an already added condition.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$condition` modifying an already added condition.
     *
     * @throws OutOfBoundsException
     */
    public function withModify(ConditionInterface $condition): ConditionsInterface;

    /**
     * Indicates whether the instance has the given `$name`.
     */
    public function contains(string $name): bool;

    /**
     * @throws OutOfRangeException
     */
    public function get(string $name): ConditionInterface;

    /**
     * @return Generator<string, ConditionInterface>
     */
    public function getGenerator(): Generator;
}
