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

namespace Chevere\Components\Router;

use FastRoute\RouteCollector;
use FastRoute\DataGenerator\GroupCountBased;
use Chevere\Interfaces\Router\RouterInterface;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RoutablesInterface;
use Chevere\Interfaces\Router\RouterIndexInterface;
use Chevere\Components\Router\RouteParsers\StrictStd;

final class Router implements RouterInterface
{
    private RouterIndexInterface $index;

    private RoutablesInterface $routables;

    private RouteCollector $routeCollector;

    public function __construct()
    {
        $this->index = new RouterIndex;
        $this->routables = new Routables;
        $this->routeCollector = new RouteCollector(new StrictStd, new GroupCountBased);
    }

    public function withAddedRoutable(RoutableInterface $routable, string $group): RouterInterface
    {
        $new = clone $this;
        $route = $routable->route();
        $new->index = $new->index->withAdded($routable, $group);
        $new->routables = $new->routables->withPut($routable);
        foreach ($route->endpoints()->getGenerator() as $key => $endpoint) {
            $new->routeCollector->addRoute($endpoint->method()::name(), $route->path()->toString(), $group);
        }

        return $new;
    }

    public function index(): RouterIndexInterface
    {
        return $this->index;
    }

    public function routables(): RoutablesInterface
    {
        return $this->routables;
    }
}
