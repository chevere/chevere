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
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Router;
use Chevere\Tests\Router\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testRouter(): void
    {
        $this->expectNotToPerformAssertions();
        $router = new Router;
        $routeName = new RouteName('my-route');
        $routePath = new RoutePath('/user/{id:\d+}/{name:\w+}/');
        $route = new Route($routeName, $routePath);
        $route = $route->withAddedEndpoint(
            new RouteEndpoint(
                new GetMethod,
                new TestController
            )
        );
        $routable = new Routable($route);
        $router = $router->withAddedRoutable($routable, 'my-group');
        $router->dispatch('GET', '/user/123/rodolfo/');
    }
}
