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

namespace Chevere\Interfaces\Route;

use Chevere\Interfaces\To\ToArrayInterface;
use Countable;

interface RouteWildcardsInterface extends ToArrayInterface, Countable
{
    public function __construct();

    /**
     * Return an instance with the specified RouteWildcardInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouteWildcardInterface.
     */
    public function withAddedWildcard(RouteWildcardInterface $wildcard): RouteWildcardsInterface;

    /**
     * @return int The count of RouteWildcardInterface objects
     */
    public function count(): int;

    /**
     * @return bool a boolean indicating whether the instance has any RouteWildcardInterface.
     */
    public function hasAny(): bool;

    /**
     * Returns a boolean indicating whether the instance has a given RouteWildcardInterface.
     */
    public function has(RouteWildcardInterface $wildcard): bool;

    /**
     * Provides access to the target RouteWildcardInterface instance.
     */
    public function get(RouteWildcardInterface $wildcard): RouteWildcardInterface;

    /**
     * Returns a boolean indicating whether the instance has RouteWildcardInterface in the given pos.
     */
    public function hasPos(int $pos): bool;

    /**
     * Provides access to the target RouteWildcardInterface instance in the given pos.
     */
    public function getPos(int $pos): RouteWildcardInterface;
}
