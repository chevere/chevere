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

namespace Chevere\Spec\Interfaces\Specs;

use Chevere\DataStructure\Interfaces\MappedInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Iterator;

/**
 * Describes the component in charge of collecting objects implementing `GroupSpecInterface`.
 */
interface GroupSpecsInterface extends MappedInterface
{
    /**
     * Return an instance with the specified `$groupSpec`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$groupSpec`.
     */
    public function withPut(GroupSpecInterface $groupSpec): self;

    /**
     * Indicates whether the instance has a group spec identified by its `$groupName`.
     */
    public function has(string $groupName): bool;

    /**
     * Returns the group spec identified by its `$groupName`.
     * @throws OutOfBoundsException
     */
    public function get(string $groupName): GroupSpecInterface;

    /**
     * @return Iterator<string, GroupSpecInterface>
     */
    public function getIterator(): Iterator;
}
