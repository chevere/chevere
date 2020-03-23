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

/**
 * A type-hinted proxy for Ds\Map storing methodName => RouteEndpointInterface
 */
final class RouteEndpointsMap
{
    use DsMapTrait;

    public function put(RouteEndpointInterface $routeEndpoint): void
    {
        $this->map->put($routeEndpoint->method()->name(), $routeEndpoint);
    }

    public function hasKey(MethodInterface $method): bool
    {
        return $this->map->hasKey($method->name());
    }

    public function get(MethodInterface $method): RouteEndpointInterface
    {
        return $this->map->get($method->name());
    }
}
