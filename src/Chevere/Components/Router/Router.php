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

use Chevere\Components\Message\Message;
use Chevere\Components\Router\RouteParsers\StrictStd;
use Chevere\Components\Var\VarStorable;
use Chevere\Exceptions\Router\RouteNotRoutableException;
use Chevere\Exceptions\Router\RouteWithoutEndpointsException;
use Chevere\Interfaces\Router\Route\RouteInterface;
use Chevere\Interfaces\Router\RouterIndexInterface;
use Chevere\Interfaces\Router\RouterInterface;
use Chevere\Interfaces\Router\RoutesInterface;
use FastRoute\DataGenerator\GroupCountBased as DataGenerator;
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
        $this->routeCollector = new RouteCollector(new StrictStd(), new DataGenerator());
    }

    public function withAddedRoute(string $group, RouteInterface $route): RouterInterface
    {
        $this->assertRoute($route);
        $new = clone $this;
        $new->index = $new->index->withAddedRoute($route, $group);
        $new->routes = $new->routes->withAdded($route);
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
