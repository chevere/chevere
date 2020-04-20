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

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Controller\Parameter;
use Chevere\Components\Controller\Parameters;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Middleware\MiddlewareName;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\TestApp\App\Middlewares\TestMiddlewareVoid;
use LogicException;
use OutOfBoundsException;
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

    public function testWithEndpoint(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test'));
        $method = new GetMethod;
        $controller = new RouteTestController;
        $endpoint = new RouteEndpoint($method, $controller);
        $route = $route->withAddedEndpoint($endpoint);
        $this->assertTrue($route->endpoints()->hasKey($method));
        $this->assertSame($endpoint, $route->endpoints()->get($method));
    }

    public function testWithEndpointWrongWildcard(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{foo}'));
        $method = new GetMethod;
        $controller = new RouteTestController;
        $endpoint = new RouteEndpoint($method, $controller);
        $this->expectException(OutOfBoundsException::class);
        $route = $route->withAddedEndpoint($endpoint);
    }

    public function testWildcardWrongParameterWithEndpoint(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{foo}'));
        $method = new GetMethod;
        $controller = new RouteTestControllerNoParams;
        $endpoint = new RouteEndpoint($method, $controller);
        $this->expectException(LogicException::class);
        $route = $route->withAddedEndpoint($endpoint);
    }

    public function testWildcardParameterWithEndpoint(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{id}'));
        $method = new GetMethod;
        $controller = new RouteTestController;
        $endpoint = new RouteEndpoint($method, $controller);
        $route = $route->withAddedEndpoint($endpoint);
        $this->assertTrue($route->endpoints()->hasKey($method));
        $pair = $controller->parameters()->map()->first()->toArray();
        $key = $pair['key'];
        $this->assertSame(
            [
                $key => $controller->parameters()->get($key)->regex()
            ],
            $route->endpoints()->get($method)->parameters()
        );
    }

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

final class RouteTestController extends Controller
{
    public function getParameters(): ControllerParametersInterface
    {
        return (new Parameters)
            ->withParameter(new Parameter('name', new Regex('/^[\w]+$/')))
            ->withParameter(new Parameter('id', new Regex('/^[0-9]+$/')));
    }

    public function run(ControllerArgumentsInterface $arguments): void
    {
        // does nothing
    }
}

final class RouteTestControllerNoParams extends Controller
{
    public function run(ControllerArgumentsInterface $arguments): void
    {
        // does nothing
    }
}
