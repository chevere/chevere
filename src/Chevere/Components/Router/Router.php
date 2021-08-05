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

use Chevere\Components\Router\RouteParsers\StrictStd;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RoutablesInterface;
use Chevere\Interfaces\Router\RouterIndexInterface;
use Chevere\Interfaces\Router\RouterInterface;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
use FastRoute\RouteCollector;

final class Router implements RouterInterface
{
    private RouterIndexInterface $index;

    private RoutablesInterface $routables;

    private RouteCollector $routeCollector;

    public function __construct()
    {
        $this->index = new RouterIndex();
        $this->routables = new Routables();
        $this->routeCollector = new RouteCollector(new StrictStd(), new DataGenerator());
    }

    public function withAddedRoutable(RoutableInterface $routable, string $group): RouterInterface
    {
        $new = clone $this;
        $route = $routable->route();
        $new->index = $new->index->withAddedRoutable($routable, $group);
        $new->routables = $new->routables->withPut($routable);
        foreach ($route->endpoints()->getGenerator() as $endpoint) {
            $new->routeCollector->addRoute(
                $endpoint->method()::name(),
                $route->path()->toString(),
                $endpoint->controller()::class
            );
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

    public function routeCollector(): RouteCollector
    {
        return $this->routeCollector;
    }
}
