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

namespace Chevere\Components\Routing;

use Chevere\Components\Route\Route;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RouterMakerInterface;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Routing\Interfaces\FsRoutesMakerInterface;
use Chevere\Components\Routing\Interfaces\RoutingInterface;

final class Routing implements RoutingInterface
{
    private FsRoutesMakerInterface $routePathIterator;

    private RouterMakerInterface $routerMaker;

    public function __construct(
        FsRoutesMakerInterface $fsRoutesMaker,
        RouterMakerInterface $routerMaker
    ) {
        $this->routePathIterator = $fsRoutesMaker;
        $this->routerMaker = $routerMaker;
        $fsRoutes = $this->routePathIterator->fsRoutes();
        for ($i = 0; $i < $fsRoutes->count(); ++$i) {
            $fsRoute = $fsRoutes->get($i);
            $routePath = $fsRoute->routePath();
            $routeDecorator = $fsRoute->routeDecorator();
            foreach ($routeDecorator->wildcards()->toArray() as $routeWildcard) {
                $routePath = $routePath->withWildcard($routeWildcard);
            }
            $routeEndpointsMaker = new RouteEndpointsIterator($fsRoute->dir());
            $routeEndpoints = $routeEndpointsMaker->routeEndpoints();
            $route = new Route($routeDecorator->name(), $routePath);
            /** @var string $key */
            foreach ($routeEndpoints->keys() as $key) {
                $route = $route->withAddedEndpoint(
                    $routeEndpoints->get($key)
                );
            }
            $this->routerMaker = $this->routerMaker
                ->withAddedRouteable(new Routeable($route), 'routing');
        }
    }

    public function router(): RouterInterface
    {
        return $this->routerMaker->router();
    }
}
