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

namespace Chevere\Router;

use Chevere\Message\Message;
use Chevere\Router\Exceptions\RouteNotRoutableException;
use Chevere\Router\Exceptions\RouteWithoutEndpointsException;
use Chevere\Router\Interfaces\Route\RouteInterface;
use Chevere\Router\Interfaces\RouterIndexInterface;
use Chevere\Router\Interfaces\RouterInterface;
use Chevere\Router\Interfaces\RoutesInterface;
use Chevere\Router\Parsers\StrictStd;
use Chevere\VarSupport\VarStorable;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use Throwable;

final class Router implements RouterInterface
{
    private RouterIndexInterface $index;

    private RoutesInterface $routes;

    private RouteCollector $routeCollector;

    public function __construct()
    {
        $this->routes = new Routes();
        $this->index = new RouterIndex();
        $this->routeCollector = new RouteCollector(new StrictStd(), new GroupCountBased());
    }

    public function withAddedRoute(string $group, RouteInterface $route): RouterInterface
    {
        $this->assertRoute($route);
        $new = clone $this;
        $new->index = $new->index->withAddedRoute($route, $group);
        $new->routes = $new->routes->withAdded($route);
        foreach ($route->endpoints()->getIterator() as $endpoint) {
            $new->routeCollector->addRoute(
                $endpoint->method()::name(),
                $route->path()->__toString(),
                $endpoint->controller()::class
            );
        }

        return $new;
    }

    public function index(): RouterIndexInterface
    {
        return $this->index;
    }

    public function routes(): RoutesInterface
    {
        return $this->routes;
    }

    public function routeCollector(): RouteCollector
    {
        return $this->routeCollector;
    }

    private function assertRoute(RouteInterface $route): void
    {
        try {
            $varStorable = new VarStorable($route);
            $varStorable->toExport();
        } catch (Throwable $e) {
            throw new RouteNotRoutableException(previous: $e);
        }
        if ($route->endpoints()->count() === 0) {
            throw new RouteWithoutEndpointsException(
                (new Message("Argument of type %className% doesn't contain any endpoint."))
                ->code('%className%', $route::class)
            );
        }
    }
}
