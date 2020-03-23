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

use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointsInterface;
use function DeepCopy\deep_copy;

final class RouteEndpoints implements RouteEndpointsInterface
{
    /** @Var RouteEndpointsMap [<string>methodName => RouteEndpointInterface] */
    private RouteEndpointsMap $routeEndpointsMap;

    public function __construct()
    {
        $this->routeEndpointsMap = new RouteEndpointsMap;
    }

    public function withAddedRouteEndpoint(RouteEndpointInterface $routeEndpoint): RouteEndpointsInterface
    {
        $new = clone $this;
        $new->storeRouteEndpoint($routeEndpoint);

        return $new;
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
