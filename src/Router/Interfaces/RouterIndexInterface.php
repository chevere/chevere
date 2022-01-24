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

namespace Chevere\Router\Interfaces;

use Chevere\Common\Interfaces\ToArrayInterface;
use Chevere\Router\Interfaces\Route\RouteInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;

/**
 * Describes the component in charge of indexing named routes.
 */
interface RouterIndexInterface extends ToArrayInterface
{
    /**
     * Return an instance with the specified `$route` added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$route` added.
     *
     * @throws InvalidArgumentException
     * @throws OverflowException
     */
    public function withAddedRoute(RouteInterface $route, string $group): self;

    /**
     * Indicates whether the instance has a route identified by its `$name`.
     */
    public function hasRouteName(string $name): bool;

    /**
     * Returns the route identifier for the given route `$name`.
     *
     * @throws OutOfBoundsException
     */
    public function getRouteIdentifier(string $name): RouteIdentifierInterface;

    /**
     * Indicates whether the instance has routes for the given `$group`.
     */
    public function hasGroup(string $group): bool;

    /**
     * Returns an array containing the route names for the given `$group`.
     *
     * @throws OutOfBoundsException
     */
    public function getGroupRouteNames(string $group): array;

    /**
     * Returns the route group for the route identified by its `$name`.
     *
     * @throws OutOfBoundsException
     */
    public function getRouteGroup(string $group): string;
}
