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

use Countable;

interface RouteEndpointsInterface extends Countable
{
    /**
     * @return string[] The known keys.
     */
    public function keys(): array;

    /**
     * @return int The number of entries.
     */
    public function count(): int;

    /**
     * Put the $routeEndpoint in the stock. It will be mapped by $method name.
     */
    public function put(RouteEndpointInterface $routeEndpoint): void;

    /**
     * Returns a boolean indicating whether the instance has $key.
     */
    public function hasKey(string $key): bool;

    /**
     * Provides access to the RouteEndpointInterface identified by $key.
     */
    public function get(string $key): RouteEndpointInterface;
}
