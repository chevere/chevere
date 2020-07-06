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
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use PHPUnit\Framework\TestCase;
use function FastRoute\simpleDispatcher;

final class RouterTest extends TestCase
{
    public function testFastRoute(): void
    {
        $this->expectNotToPerformAssertions();
        $dispatcher = simpleDispatcher(
            function (RouteCollector $r)
            {
                $r->addRoute('GET', '/users', 'get_all_users_handler');
                $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
                $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
            },
            // [
            //     'cacheFile' => __DIR__ . '/route.cache',
            // ]
        );
        $routeInfo = $dispatcher->dispatch('GET', '/user/321/123');
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                // ... call $handler with $vars
                break;
        }
        // xdd($vars);
    }

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
        // xdd($router);
    }
}
