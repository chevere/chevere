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

namespace Chevere\Interfaces\Router;

use Chevere\Interfaces\To\ToArrayInterface;
use Chevere\Interfaces\Route\RouteInterface;

interface RouterIndexInterface extends ToArrayInterface
{
    /**
     * Return an instance with the specified values.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified values.
     */
    public function withAdded(RoutableInterface $routable, string $group): RouterIndexInterface;

    public function hasRouteName(string $routeName): bool;

    public function getRouteIdentifier(string $routeName): RouteIdentifierInterface;

    public function hasGroup(string $group): bool;

    public function getGroupRouteNames(string $group): array;

    public function getRouteGroup(string $routeName): string;
}
