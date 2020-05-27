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

namespace Chevere\Tests\Route;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\ControllerResponse;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\PostMethod;
use Chevere\Components\Middleware\MiddlewareName;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Core\RangeException;
use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use Chevere\Interfaces\Controller\ControllerResponseInterface;
use Chevere\Interfaces\Route\RouteInterface;
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

    public function testWithAddedEndpoint(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test'));
        $method = new GetMethod;
        $controller = new RouteTestController;
        $endpoint = new RouteEndpoint($method, $controller);
        $route = $route->withAddedEndpoint($endpoint);
        $this->assertTrue($route->endpoints()->hasKey($method->name()));
        $this->assertSame($endpoint, $route->endpoints()->get($method->name()));
    }

    public function testWithAddedEndpointWrongWildcard(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{foo}'));
        $method = new GetMethod;
        $controller = new RouteTestController;
        $endpoint = new RouteEndpoint($method, $controller);
        $this->expectException(OutOfBoundsException::class);
        $route = $route->withAddedEndpoint($endpoint);
    }

    public function testWithAddedEndpointWildcardWrongParameter(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{foo}'));
        $method = new GetMethod;
        $controller = new RouteTestControllerNoParams;
        $endpoint = new RouteEndpoint($method, $controller);
        $this->expectException(LogicException::class);
        $route = $route->withAddedEndpoint($endpoint);
    }

    public function testWithAddedEndpointWildcardParameter(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{id}'));
        $method = new GetMethod;
        $controller = new RouteTestController;
        $endpoint = new RouteEndpoint($method, $controller);
        $route = $route->withAddedEndpoint($endpoint);
        $this->assertTrue($route->endpoints()->hasKey($method->name()));
        $this->assertSame(
            [],
            $route->endpoints()->get($method->name())->parameters()
        );
    }

    public function testWithAddedEnpointOverride(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{id}'));
        $endpoint = new RouteEndpoint(new GetMethod, new RouteTestController);
        $route = $route->withAddedEndpoint($endpoint);
        $this->expectException(OverflowException::class);
        $route->withAddedEndpoint($endpoint);
    }

    public function testWithAddedEndpointRegexConflict(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{id}'));
        $route = $route->withAddedEndpoint(
            new RouteEndpoint(new GetMethod, new RouteTestController)
        );
        $this->expectException(RangeException::class);
        $route = $route->withAddedEndpoint(
            new RouteEndpoint(new PostMethod, new RouteTestControllerRegexConflict)
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
        return (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('id', new Regex('/^[0-9]+$/'))
            );
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponse(true);
    }
}

final class RouteTestControllerNoParams extends Controller
{
    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponse(true);
    }
}

final class RouteTestControllerRegexConflict extends Controller
{
    public function getParameters(): ControllerParametersInterface
    {
        return (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('id', new Regex('/^\W+$/'))
            );
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponse(true);
    }
}
