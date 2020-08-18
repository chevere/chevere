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

namespace Chevere\Interfaces\Permission;

use Generator;

/**
 * Describes the component in charge of collecting objects implementing `RangeInterface`.
 */
interface RangesInterface
{
    /**
     * Return an instance with the specified `$range` added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$range` added.
     *
     * @throws OverflowException
     */
    public function withAdded(RangeInterface $range): RangesInterface;

    /**
     * Return an instance with the specified `$range` modifying an already added condition.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$range` modifying an already added condition.
     *
     * @throws OutOfBoundsException
     */
    public function withModify(RangeInterface $range): RangesInterface;

    /**
     * Indicates whether the instance has the given `$name`.
     */
    public function contains(string $name): bool;

    /**
     * @throws OutOfRangeException
     */
    public function get(string $name): RangeInterface;

    /**
     * @return Generator<string, RangeInterface>
     */
    public function getGenerator(): Generator;
}
