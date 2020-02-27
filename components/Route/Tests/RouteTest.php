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

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\PostMethod;
use Chevere\Components\Middleware\MiddlewareName;
use Chevere\Components\Route\WildcardMatch;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\Wildcard;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\Interfaces\WildcardInterface;
use Chevere\TestApp\App\Controllers\TestController;
use Chevere\TestApp\App\Middlewares\TestMiddlewareVoid;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    private function getRoute(string $path): RouteInterface
    {
        return new Route(
            new PathUri($path)
        );
    }

    public function testConstruct(): void
    {
        $pathUri = new PathUri('/');
        $route = new Route($pathUri);
        $this->assertSame($pathUri, $route->pathUri());
        $this->assertSame(__FILE__, $route->maker()['file']);
        $this->assertFalse($route->hasMiddlewareNameCollection());
        $this->assertFalse($route->hasName());
        $this->expectException(MethodNotFoundException::class);
        $route->controllerName(new GetMethod);
    }

    public function testWithName(): void
    {
        $name = new RouteName('name-test');
        $route = $this->getRoute('/test')
            ->withName($name);
        $this->assertTrue($route->hasName());
        $this->assertSame($name, $route->name());
    }

    // public function testWithAddedMethod(): void
    // {
    //     $method = new GetMethod();
    //     $route = $this->getRoute('/test')
    //         ->withAddedMethod(
    //             $method,
    //             new ControllerName(TestController::class)
    //         );
    //     $this->assertSame(TestController::class, $route->controllerName($method)->toString());
    //     $this->expectException(MethodNotFoundException::class);
    //     $route->controllerName(new PostMethod());
    // }

    // public function testWithAddedMiddleware(): void
    // {
    //     $middlewareName = new MiddlewareName(TestMiddlewareVoid::class);
    //     $route = $this->getRoute('/test')
    //         ->withAddedMiddlewareName($middlewareName);
    //     $this->assertTrue($route->middlewareNameCollection()->hasAny());
    //     $this->assertTrue($route->middlewareNameCollection()->has($middlewareName));
    // }
}
