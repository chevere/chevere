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

namespace Chevere\Tests\Router;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Components\Router\Router;
use Chevere\Exceptions\Router\RouteNotRoutableException;
use Chevere\Exceptions\Router\RouteWithoutEndpointsException;
use Chevere\Tests\Router\_resources\src\TestController;
use FastRoute\RouteCollector;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testConstruct(): void
    {
        $router = new Router();
        $this->assertSame([], $router->index()->toArray());
        $this->assertCount(0, $router->routes());
    }

    public function testRouter(): void
    {
        $routePath = new RoutePath('/🐘/{id:\d+}/{name:\w+}');
        $route = new Route('test', $routePath);
        $route = $route->withAddedEndpoint(
            new RouteEndpoint(
                new GetMethod(),
                new TestController()
            )
        );
        $router = (new Router())
            ->withAddedRoute(route: $route, group: 'my-group');
        $this->assertCount(1, $router->routes());
        $this->assertInstanceOf(RouteCollector::class, $router->routeCollector());
    }

    public function testConstructInvalidArgument(): void
    {
        $route = new Route('test', new RoutePath('/test'));
        $this->expectException(RouteWithoutEndpointsException::class);
        (new Router())
            ->withAddedRoute(route: $route, group: 'my-group');
    }

    public function testNotExportable(): void
    {
        $route = new Route('test', new RoutePath('/test'));
        $route->resource = fopen('php://output', 'r+');
        $this->expectException(RouteNotRoutableException::class);
        (new Router())
            ->withAddedRoute(route: $route, group: 'my-group');
    }
}
