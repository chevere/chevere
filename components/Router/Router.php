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

use Chevere\Components\Router\RouteParser\StrictStd;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RouterInterface;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;

final class Router implements RouterInterface
{
    private RouteCollector $routeCollector;

    public function __construct()
    {
        $this->routeCollector = new RouteCollector(new StrictStd, new GroupCountBased);
    }

    public function withAddedRoutable(RoutableInterface $routable, string $group): RouterInterface
    {
        $new = clone $this;
        $route = $routable->route();
        foreach ($route->endpoints()->getGenerator() as $key => $endpoint) {
            $new->routeCollector->addRoute($endpoint->method()::name(), $route->path()->toString(), 'eee');
        }
        // xdd($route->path()->toString(), $new->routeCollector);

        return $new;
    }
}

// use function FastRoute\cachedDispatcher;
// use function FastRoute\simpleDispatcher;
