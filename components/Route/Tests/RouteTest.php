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

namespace Chevere\Components\Route\Tests;

use Chevere\Components\Middleware\MiddlewareName;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\TestApp\App\Controllers\TestController;
use Chevere\TestApp\App\Middlewares\TestMiddlewareVoid;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function testConstruct(): void
    {
        $routeName = new RouteName('test');
        $routePath = new RoutePath('/test');
        $route = new Route($routeName, $routePath);
        $line = __LINE__ - 1;
        $this->assertSame($routeName, $route->name());
        $this->assertSame($routePath, $route->path());
        $this->assertSame([
            'file' => __FILE__,
            'line' => $line,
            'function' => '__construct',
            'class' => Route::class,
            'type' => '->'
        ], $route->maker());
    }

    public function testWithAddedEndpoint(): void
    {
    }

    // public function testWithAddedMethodController(): void
    // {
    //     $method = new GetMethod();
    //     $route = $this->getRoute('test', '/test')
    //         ->withAddedMethodController(
    //             new MethodControllerName(
    //                 new GetMethod,
    //                 new TestController
    //             )
    //         );
    //     $this->assertSame(TestController::class, $route->controllerFor($method)->toString());
    //     $this->expectException(MethodNotFoundException::class);
    // }

    public function testWithAddedMiddleware(): void
    {
        $middlewareName = new MiddlewareName(TestMiddlewareVoid::class);
        $route = $this->getRoute('test', '/test')
            ->withAddedMiddlewareName($middlewareName);
        $this->assertTrue($route->middlewareNameCollection()->hasAny());
        $this->assertTrue($route->middlewareNameCollection()->has($middlewareName));
    }

    private function getRoute(string $name, string $path): RouteInterface
    {
        return new Route(new RouteName($name), new RoutePath($path));
    }
}
