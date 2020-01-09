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

namespace Chevere\Components\Route\Contracts;

use Chevere\Components\Common\Contracts\ToArrayContract;

/**
 * @method public array toArray() Provides access to the collection array.
 */
interface WildcardCollectionContract extends ToArrayContract
{
    public function __construct(WildcardContract ...$wildcards);

    /**
     * Return an instance with the specified WildcardContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified WildcardContract.
     */
    public function withAddedWildcard(WildcardContract $wildcard): WildcardCollectionContract;

    /**
     * Returns a boolean indicating whether the instance has any WildcardContract.
     */
    public function hasAny(): bool;

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
}
