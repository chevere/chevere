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

namespace Chevere\Interfaces\Router\Route;

use Chevere\Interfaces\Common\ToArrayInterface;
use Chevere\Interfaces\DataStructure\MappedInterface;
use Traversable;

/**
 * Describes the component in charge of collecting objects implementing `RouteWildcardInterface`.
 */
interface WildcardsInterface extends MappedInterface, ToArrayInterface
{
    /**
     * Return an instance with the specified `$wildcard`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$wildcard`.
     *
     * This method should overrides existing wildcards.
     */
    public function withPut(RouteWildcardInterface $wildcard): self;

    /**
     * Returns a boolean indicating whether the instance has a given RouteWildcardInterface.
     */
    public function has(string $wildcardName): bool;

    /**
     * Provides access to the target RouteWildcardInterface instance.
     */
    public function get(string $wildcardName): RouteWildcardInterface;

    /**
     * Returns a boolean indicating whether the instance has RouteWildcardInterface in the given pos.
     */
    public function hasPos(int $pos): bool;

    /**
     * Provides access to the target RouteWildcardInterface instance in the given pos.
     */
    public function getPos(int $pos): RouteWildcardInterface;

    /**
     * @return Traversable<int , RouteWildcardInterface>
     */
    public function getIterator(): Traversable;
}
