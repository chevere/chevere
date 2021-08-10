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
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Components\Router\Routes;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RoutesTest extends TestCase
{
    public function testWithPut(): void
    {
        $route = new Route(
            new RoutePath('/some-path')
        );
        $key = $route->path()->toString();
        $route = $route->withAddedEndpoint(
            new RouteEndpoint(
                new GetMethod(),
                new TestController()
            )
        );
        $routes = (new Routes())->withPut($route);
        $this->assertFalse($routes->has('test', 'some'));
        $this->assertTrue($routes->has($key));
        $this->assertSame($route, $routes->get($key));
        $this->expectException(OutOfBoundsException::class);
        $routes->get('not-found');
    }
}
