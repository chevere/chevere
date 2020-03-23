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
use function DeepCopy\deep_copy;

final class RouteEndpoints implements RouteEndpointsInterface
{
    /** @Var RouteEndpointsMap [<string>methodName => RouteEndpointInterface] */
    private RouteEndpointsMap $routeEndpointsMap;

    public function __construct(RouteEndpointInterface ...$routeEndpoint)
    {
        $this->routeEndpointsMap = new RouteEndpointsMap;
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
        return $this->routeEndpointsMap->hasKey($method);
    }

    public function getRouteEndpoint(MethodInterface $method): RouteEndpointInterface
    {
        if (!$this->routeEndpointsMap->hasKey($method)) {
            throw new MethodNotFoundException(
                (new Message('Method %method% not found'))
                    ->code('%method%', $method::name())
                    ->toString()
            );
        }

        return $this->routeEndpointsMap->get($method);
    }

    public function routeEndpointsMap(): RouteEndpointsMap
    {
        return deep_copy($this->routeEndpointsMap);
    }

    private function storeRouteEndpoint(RouteEndpointInterface $routeEndpoint): void
    {
        $this->routeEndpointsMap->put($routeEndpoint);
    }
}
