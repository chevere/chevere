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

use function Chevere\Components\Router\route;
use function Chevere\Components\Router\routes;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Router\Route\RouteEndpointInterface;
use Chevere\Tests\Router\Route\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RouterFunctionsTest extends TestCase
{
    public function testFunctionRoute(): void
    {
        $controller = new TestController();
        foreach (RouteEndpointInterface::KNOWN_METHODS as $httpMethod => $className) {
            $route = route('/test/', ...[$httpMethod => $controller]);
            $this->assertTrue($route->endpoints()->hasKey($httpMethod));
            $this->assertCount(1, $route->endpoints());
            $this->assertSame($controller, $route->endpoints()->get($httpMethod)->controller());
        }
    }

    public function testFunctionRouteError(): void
    {
        $controller = new TestController();
        $this->expectException(InvalidArgumentException::class);
        route('/test/', TEST: $controller);
    }

    public function testFunctionRoutes(): void
    {
        $path = '/test/';
        $routes = routes(
            myRoute: route($path, GET: new TestController())
        );
        $this->assertTrue($routes->has($path));
        $this->assertSame('myRoute', $routes->getName($path));
        $this->expectException(OutOfBoundsException::class);
        $routes->getName('404');
    }
}
