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

namespace Chevere\Components\Router\Interfaces;

use Chevere\Components\Route\Interfaces\RouteInterface;

/**
 * Provides access to the router index.
 */
interface RouterIndexInterface
{
    /**
     * Return an instance with the specified values.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified values.
     */
    public function withAdded(RouteableInterface $routeable, int $id, string $group): RouterIndexInterface;

    /**
     * Returns a boolean indicating whether the instance has the given index.
     */
    public function has(int $id): bool;

    /**
     * Returns a boolean indicating whether the instance has the given key indexed.
     */
    public function hasKey(string $key): bool;

    /**
     * Retrieves the id for the given key (RoutePath string)
     */
    public function idForKey(string $key): int;

    /**
     * Provides access to the RouteIdentifier instance identified by its internal id.
     */
    public function get(int $id): RouteIdentifierInterface;

    public function toArray(): array;
}
