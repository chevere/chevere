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

use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\MethodController;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\PostMethod;
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
    private function getRoute(string $name, string $path): RouteInterface
    {
        return new Route(
            new RouteName($name),
            new RoutePath($path)
        );
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
}
