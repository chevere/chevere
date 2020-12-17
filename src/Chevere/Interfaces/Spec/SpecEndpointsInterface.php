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
 * Describes the component in charge of collecting objects implementing `RouteEndpointSpecInterface`.
 */
interface SpecEndpointsInterface extends MappedInterface
{
    /**
     * Return an instance with the specified `$routeEndpointSpec`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$routeEndpointSpec`.
     */
    public function withPut(RouteEndpointSpecInterface $routeEndpointSpec): self;

    /**
     * Indicates whether the instance has a route endpoint identified by its `$methodName`.
     */
    public function has(string $methodName): bool;

    /**
     * Returns the group spec identified by its `$methodName`.
     * @throws OutOfBoundsException
     */
    public function get(string $methodName): string;
}
