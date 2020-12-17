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

namespace Chevere\Interfaces\Spec;

use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\DataStructures\MappedInterface;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecInterface;

/**
 * Describes the component in charge of indexing endpoint specs for each route.
 */
interface SpecIndexInterface extends MappedInterface
{
    /**
     * Return an instance with the specified `$routeEndpointSpec` for `$routeName`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$routeEndpointSpec` for `$routeName`.
     */
    public function withAddedRoute(string $routeName, RouteEndpointSpecInterface $routeEndpointSpec): self;

    /**
     * Indicates whether the instance has a route endpoint spec for `$routeName` at the given `$methodName`.
     */
    public function has(string $routeName, string $methodName): bool;

    /**
     * Returns the spec path.
     * @throws OutOfBoundsException
     */
    public function get(string $routeName, string $methodName): string;
}
