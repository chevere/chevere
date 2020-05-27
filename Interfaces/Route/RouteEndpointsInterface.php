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

use Chevere\Interfaces\DataStructures\DsMapInterface;
use Countable;

interface RouteEndpointsInterface extends DsMapInterface
{
    /**
     * @return string[] The known keys.
     */
    public function keys(): array;

    /**
     * Return an instance with the specified RouteEndpoint.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouteEndpoint.
     */
    public function withPut(RouteEndpointInterface $routeEndpoint): RouteEndpointsInterface;

    /**
     * Returns a boolean indicating whether the instance has $key.
     */
    public function hasKey(string $key): bool;

    /**
     * Provides access to the RouteEndpointInterface identified by $key.
     */
    public function get(string $key): RouteEndpointInterface;
}
