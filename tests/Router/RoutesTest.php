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

use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Components\Router\Routes;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use PHPUnit\Framework\TestCase;

final class RoutesTest extends TestCase
{
    public function testWithAdded(): void
    {
        $name = 'test';
        $route = (new Route(
            name: $name,
            path: new RoutePath('/some-path')
        ));
        $key = $route->path()->__toString();
        $routes = new Routes();
        $routesWithAdded = $routes
            ->withAdded($route);
        $this->assertNotSame($routes, $routesWithAdded);
        $this->assertTrue($routesWithAdded->has($key));
        $this->assertSame($route, $routesWithAdded->get($key));
        $this->expectException(OutOfBoundsException::class);
        $routesWithAdded->get('not-found');
    }

    public function testWithAddedNameCollision(): void
    {
        $name = 'test';
        $route = new Route(
            name: $name,
            path: new RoutePath('/some-path')
        );
        $key = $route->path()->__toString();
        $routes = (new Routes())
            ->withAdded($route);
        $this->expectException(OverflowException::class);
        $this->expectExceptionCode(Routes::EXCEPTION_CODE_TAKEN_NAME);
        $routes->withAdded(
            new Route(
                name: $name,
                path: new RoutePath('/some-alt-path')
            )
        );
    }

    public function testWithAddedPathCollision(): void
    {
        $routePath = new RoutePath('/some-path');
        $route = new Route(
            name: 'test',
            path: $routePath
        );
        $key = $route->path()->__toString();
        $routes = (new Routes())
            ->withAdded($route);
        $this->expectException(OverflowException::class);
        $this->expectExceptionCode(Routes::EXCEPTION_CODE_TAKEN_PATH);
        $routes->withAdded(
            new Route(
                name: 'test-2',
                path: $routePath
            )
        );
    }
}
