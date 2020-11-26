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
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\PostMethod;
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Route\RouteEndpointConflictException;
use Chevere\Exceptions\Route\RouteWildcardConflictException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;
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
        $route->withAddedEndpoint($endpoint);
    }

    public function testWithAddedEndpointNoParams(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{foo}'));
        $method = new GetMethod;
        $controller = new RouteTestControllerNoParams;
        $endpoint = new RouteEndpoint($method, $controller);
        $this->expectException(InvalidArgumentException::class);
        $route->withAddedEndpoint($endpoint);
    }

    public function testWithAddedEndpointWildcardParameter(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{id:[0-9]+}'));
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

    public function testWithAddedEndpointOverride(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{id:[0-9]+}'));
        $endpoint = new RouteEndpoint(new GetMethod, new RouteTestController);
        $route = $route->withAddedEndpoint($endpoint);
        $this->expectException(OverflowException::class);
        $route->withAddedEndpoint($endpoint);
    }

    public function testWithAddedEndpointConflict(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{id:[0-9]+}'));
        $endpoint1 = new RouteEndpoint(new GetMethod, new RouteTestController);
        $endpoint2 = new RouteEndpoint(new PostMethod, new RouteTestControllerRegexConflict);
        $route = $route->withAddedEndpoint($endpoint1);
        $this->expectException(RouteEndpointConflictException::class);
        $route->withAddedEndpoint($endpoint2);
    }

    public function testWithAddedEndpointWildcardConflict(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test/{id:\w+}'));
        $endpoint = new RouteEndpoint(new GetMethod, new RouteTestController);
        $this->expectException(RouteWildcardConflictException::class);
        $route->withAddedEndpoint($endpoint);
    }
}

final class RouteTestController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(
                (new StringParameter('id'))
                    ->withRegex(new Regex('/^[0-9]+$/'))
            );
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess(new Parameters, []);
    }
}

final class RouteTestControllerNoParams extends Controller
{
    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess(new Parameters, []);
    }
}

final class RouteTestControllerRegexConflict extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(
                (new StringParameter('id'))
                    ->withRegex(new Regex('/^\W+$/'))
            );
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess(new Parameters, []);
    }
}
