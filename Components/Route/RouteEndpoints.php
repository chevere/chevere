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

namespace Chevere\Components\Route;

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use Chevere\Interfaces\Route\RouteEndpointsInterface;

final class RouteEndpoints implements RouteEndpointsInterface
{
    use DsMapTrait;

    public function put(RouteEndpointInterface $routeEndpoint): void
    {
        /** @var \Ds\TKey $key */
        $key = $routeEndpoint->method()->name();
        $this->map->put($key, $routeEndpoint);
    }

    public function hasKey(string $key): bool
    {
        /** @var \Ds\TKey $key */
        return $this->map->hasKey($key);
    }

    public function get(string $key): RouteEndpointInterface
    {
        /**
         * @var \Ds\TKey $key
         * @var RouteEndpointInterface $return
         */
        $return = $this->map->get($key);

        return $return;
    }
}
