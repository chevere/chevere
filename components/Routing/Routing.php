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

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Route\Route;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RouterMakerInterface;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Routing\Interfaces\RoutePathIteratorInterface;
use Chevere\Components\Routing\Interfaces\RoutingInterface;

final class Routing implements RoutingInterface
{
    private RoutePathIteratorInterface $routePathIterator;

    private RouterMakerInterface $routerMaker;

    private RoutePathInterface $routePath;

    private RouteDecoratorInterface $routeDecorator;

    public function __construct(
        RoutePathIteratorInterface $routePathIterator,
        RouterMakerInterface $routerMaker
    ) {
        $this->routePathIterator = $routePathIterator;
        $this->routerMaker = $routerMaker;
        $decoratedRoutes = $this->routePathIterator->decoratedRoutes();
        for ($i = 0; $i < $decoratedRoutes->count(); ++$i) {
            $decoratedRoute = $decoratedRoutes->get($i);
            $this->routePath = $decoratedRoute->routePath();
            $this->routeDecorator = $decoratedRoute->routeDecorator();
            foreach ($this->routeDecorator->wildcards()->toArray() as $routeWildcard) {
                $this->routePath = $this->routePath->withWildcard($routeWildcard);
            }
            $dir = new Dir(new Path(dirname($this->routeDecorator->whereIs()) . '/'));
            $routeEndpointsMaker = new RouteEndpointsIterator($dir);
            $routeEndpoints = $routeEndpointsMaker->routeEndpoints();
            $route = new Route($this->routeDecorator->name(), $this->routePath);
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
