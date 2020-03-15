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
use Chevere\Components\Http\MethodController;
use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RoutePath;
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
        $routePaths = $this->routePathIterator->objects();
        $routePaths->rewind();
        while ($routePaths->valid()) {
            $this->routePath = $routePaths->current();
            $this->routeDecorator = $routePaths->getInfo();
            foreach ($this->routeDecorator->wildcards()->toArray() as $routeWildcard) {
                $this->routePath = $this->routePath->withWildcard($routeWildcard);
            }
            $dir = new Dir(new Path(dirname($this->routeDecorator->whereIs()) . '/'));
            $routeEndpointIterator = new RouteEndpointIterator($dir);
            $endpoints = $routeEndpointIterator->objects();
            $routeEndpointIterator->objects()->rewind();
            $route = new Route($this->routeDecorator->name(), $this->routePath);
            while ($endpoints->valid()) {
                $this->routeEndpoint = $endpoints->current();
                $route = $route->withAddedMethodController(
                    new MethodController(
                        $this->routeEndpoint->method(),
                        $this->routeEndpoint->controller()
                    )
                );
                $routeable = new Routeable($route);
                $endpoints->next();
            }
            $this->routerMaker = $this->routerMaker
                ->withAddedRouteable($routeable, 'campos');
            $routePaths->next();
        }
    }

    public function routeMaker(): RouterMakerInterface
    {
        return $this->routerMaker;
    }
}
