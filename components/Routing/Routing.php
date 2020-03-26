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
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
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

    private RouteEndpointInterface $routeEndpoint;

    public function __construct(
        RoutePathIteratorInterface $routePathIterator,
        RouterMakerInterface $routerMaker
    ) {
        $this->routePathIterator = $routePathIterator;
        $this->routerMaker = $routerMaker;
        $routePaths = $this->routePathIterator->routePathObjects();
        $routePaths->rewind();
        while ($routePaths->valid()) {
            $this->routePath = $routePaths->current();
            $this->routeDecorator = $routePaths->getInfo();
            foreach ($this->routeDecorator->wildcards()->toArray() as $routeWildcard) {
                $this->routePath = $this->routePath->withWildcard($routeWildcard);
            }
            $dir = new Dir(new Path(dirname($this->routeDecorator->whereIs()) . '/'));
            $routeEndpointsMaker = new RouteEndpointsMaker($dir);
            $routeEndpoints = $routeEndpointsMaker->routeEndpointsMap();
            $route = new Route($this->routeDecorator->name(), $this->routePath);
            /** @var RouteEndpoint $routeEndpoint */
            foreach ($routeEndpoints->map() as $routeEndpoint) {
                $route = $route->withAddedEndpoint($routeEndpoint);
                $routeable = new Routeable($route);
            }
            $this->routerMaker = $this->routerMaker
                ->withAddedRouteable($routeable, 'CAMPOS');
            $routePaths->next();
        }
    }

    public function routeMaker(): RouterMakerInterface
    {
        return $this->routerMaker;
    }
}
