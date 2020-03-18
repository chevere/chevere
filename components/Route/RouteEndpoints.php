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

use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointsInterface;
use Ds\Map;

final class RouteEndpoints implements RouteEndpointsInterface
{
    private Map $map;

    public function __construct(RouteEndpointInterface ...$routeEndpoint)
    {
        $this->map = new Map;
        foreach ($routeEndpoint as $object) {
            $this->storeRouteEndpoint($object);
        }
    }

    public function withAddedRouteEndpoint(RouteEndpointInterface $routeEndpoint): RouteEndpointsInterface
    {
        $new = clone $this;
        $new->storeRouteEndpoint($routeEndpoint);

        return $new;
    }

    public function hasMethod(MethodInterface $method): bool
    {
        return $this->map->hasKey($method::name());
    }

    public function getMethod(MethodInterface $method): RouteEndpointInterface
    {
        if (!$this->map->hasKey($method::name())) {
            throw new MethodNotFoundException(
                (new Message('Method %method% not found'))
                    ->code('%method%', $method::name())
                    ->toString()
            );
        }

        return $this->map->get($method::name());
    }

    public function routeEndpointsMap(): RouteEndpointsMap
    {
        return new RouteEndpointsMap($this->map);
    }

    private function storeRouteEndpoint(RouteEndpointInterface $routeEndpoint): void
    {
        $this->map->put($routeEndpoint->method()->name(), $routeEndpoint);
    }
}
