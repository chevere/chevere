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
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Router;
use Chevere\Interfaces\Router\RouterInterface;
use Chevere\Interfaces\Routing\FsRoutesMakerInterface;
use Chevere\Interfaces\Routing\RoutingInterface;

final class Routing implements RoutingInterface
{
    private FsRoutesMakerInterface $routePathIterator;

    private RouterInterface $router;

    public function __construct(FsRoutesMakerInterface $fsRoutesMaker)
    {
        $this->routePathIterator = $fsRoutesMaker;
        $this->router = new Router;
        $fsRoutes = $this->routePathIterator->fsRoutes();
        for ($i = 0; $i < $fsRoutes->count(); ++$i) {
            $fsRoute = $fsRoutes->get($i);
            $routePath = $fsRoute->routePath();
            $routeDecorator = $fsRoute->routeDecorator();
            foreach ($routeDecorator->wildcards()->getGenerator() as $routeWildcard) {
                $routePath = $routePath->withWildcard($routeWildcard); // @codeCoverageIgnore
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
            $this->router = $this->router
                ->withAddedRoutable(new Routable($route), 'routing');
        }
    }

    public function router(): RouterInterface
    {
        return $this->router;
    }
}
