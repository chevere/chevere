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

use Chevere\Http\Exceptions\HttpMethodNotAllowedException;
use Chevere\Router\Interfaces\Route\RouteEndpointInterface;
use function Chevere\Router\route;
use function Chevere\Router\routes;
use Chevere\Tests\Router\Route\_resources\src\TestController;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RouterFunctionsTest extends TestCase
{
    public function testFunctionRoute(): void
    {
        $controller = new TestController();
        foreach (RouteEndpointInterface::KNOWN_METHODS as $httpMethod => $className) {
            $route = route('/test/', $className, ...[$httpMethod => $controller]);
            $this->assertSame($className, $route->name());
            $this->assertTrue($route->endpoints()->hasKey($httpMethod));
            $this->assertCount(1, $route->endpoints());
            $this->assertSame($controller, $route->endpoints()->get($httpMethod)->controller());
        }
    }

    public function testFunctionRouteBadPath(): void
    {
        $controller = new TestController();
        $this->expectException(InvalidArgumentException::class);
        route('test', 'name', GET: $controller);
    }

    public function testFunctionRouteBadHttpMethod(): void
    {
        $controller = new TestController();
        $this->expectException(HttpMethodNotAllowedException::class);
        route('/test/', 'name', TEST: $controller);
    }

    public function testFunctionRoutes(): void
    {
        $name = 'test';
        $path = '/test/';
        $route = route(
            name: $name,
            path: $path,
            GET: new TestController()
        );
        $routes = routes(myRoute: $route);
        $this->assertTrue($routes->has($path));
        $this->assertSame($route, $routes->get($path));
    }
}
