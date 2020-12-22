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
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Components\Router\Router;
use Chevere\Tests\Router\_resources\src\TestController;
use FastRoute\RouteCollector;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testConstruct(): void
    {
        $router = new Router();
        $this->assertSame([], $router->index()->toArray());
        $this->assertCount(0, $router->routables());
    }

    public function testRouter(): void
    {
        $routePath = new RoutePath('/user/{id:\d+}/{name:\w+}');
        $route = new Route($routePath);
        $route = $route->withAddedEndpoint(
            new RouteEndpoint(
                new GetMethod(),
                new TestController()
            )
        );
        $routable = new Routable($route);
        $router = (new Router())->withAddedRoutable($routable, 'my-group');
        $this->assertInstanceOf(RouteCollector::class, $router->routeCollector());
    }
}
