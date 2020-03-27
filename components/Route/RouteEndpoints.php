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
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;

final class RouteEndpoints
{
    use DsMapTrait;

    public function put(RouteEndpointInterface $routeEndpoint): void
    {
        /** @var \Ds\TKey $key */
        $key = $routeEndpoint->method()->name();
        $this->map->put($key, $routeEndpoint);
    }

    public function hasKey(MethodInterface $method): bool
    {
        /** @var \Ds\TKey $key */
        $key = $method->name();

        return $this->map->hasKey($key);
    }

    public function get(MethodInterface $method): RouteEndpointInterface
    {
        /** @var \Ds\TKey */
        $key = $method->name();
        /** @var RouteEndpointInterface */
        $return = $this->map->get($key);

        return $return;
    }
}
