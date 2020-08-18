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
 * Describes the component in charge of collecting objects implementing `EnumInterface`.
 */
interface EnumsInterface
{
    /**
     * Return an instance with the specified `$enum` added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$enum` added.
     *
     * @throws OverflowException
     */
    public function withAdded(EnumInterface $enum): EnumsInterface;

    /**
     * Return an instance with the specified `$enum` modifying an already added condition.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$enum` modifying an already added condition.
     *
     * @throws OutOfBoundsException
     */
    public function withModify(EnumInterface $enum): EnumsInterface;

    /**
     * Indicates whether the instance has the given `$name`.
     */
    public function contains(string $name): bool;

    /**
     * @throws OutOfRangeException
     */
    public function get(string $name): EnumInterface;

    /**
     * @return Generator<string, EnumInterface>
     */
    public function getGenerator(): Generator;
}
