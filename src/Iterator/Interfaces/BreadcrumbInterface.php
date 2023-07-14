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

namespace Chevere\Iterator\Interfaces;

use Chevere\Common\Interfaces\ToArrayInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Countable;
use Stringable;

/**
 * Describe a general purpose iterator companion.
 */
interface BreadcrumbInterface extends ToArrayInterface, Stringable, Countable
{
    /**
     * Returns an string representation of the object.
     *
     * ```php
     * return '[item0][item1][itemN]...[itemN+1]';
     * ```
     */
    public function __toString(): string;

    /**
     * Indicates whether the instance has the given position.
     */
    public function has(int $pos): bool;

    /**
     * Returns the current breadcrumb position.
     */
    public function pos(): int;

    /**
     * Return an instance with the specified added item.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added item.
     */
    public function withAdded(string $item): self;

    /**
     * Return an instance with the specified pos removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified pos removed.
     *
     * @throws OutOfBoundsException
     */
    public function withRemoved(int $pos): self;

    /**
     * Returns an array representation of the object.
     *
     * ```php
     * return [0 => 'item',];
     * ```
     * @return array<int, string>
     */
    public function toArray(): array;
}
