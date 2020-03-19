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

use Chevere\Components\DataStructures\DsMap;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Ds\Map;

/**
 * A type-hinted proxy for Ds\Map storing MethodInterface => RouteEndpointInterface
 */
final class RouteEndpointsMap extends DsMap
{
    public function hasKey(MethodInterface $method): bool
    {
        return $this->map->hasKey($method::name());
    }

    public function get(MethodInterface $method): RouteEndpointInterface
    {
        return $this->map->get($method::name());
    }

    public function put(MethodInterface $method, RouteEndpointInterface $routeEndpoint): void
    {
        $this->map->put($method::name(), $routeEndpoint);
    }
}
