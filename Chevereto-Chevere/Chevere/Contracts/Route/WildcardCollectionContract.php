<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Contracts\Route;

use ArrayIterator;
use IteratorAggregate;

interface WildcardCollectionContract extends IteratorAggregate
{
    /**
     * Creates a new instance.
     */
    public function __construct(WildcardContract ...$wildcard);

    /**
     * Return an instance with the specified WildcardContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified WildcardContract.
     */
    public function withAddedWildcard(WildcardContract $wildcard): WildcardCollectionContract;

    /**
     * Returns a boolean indicating whether the instance has a given WildcardContract.
     */
    public function has(WildcardContract $wildcard): bool;

    /**
     * Provides access to the target WildcardContract instance.
     */
    public function get(WildcardContract $wildcard): WildcardContract;

    /**
     * Returns a boolean indicating whether the instance has WildcardContract in the given pos.
     */
    public function hasPos(int $pos): bool;

    /**
     * Provides access to the target WildcardContract instance in the given pos.
     */
    public function getPos(int $pos): WildcardContract;

    /**
     * Provides object as array access.
     */
    public function getIterator(): ArrayIterator;

    /**
     * Provides access to the collection array.
     */
    public function toArray(): array;
}
