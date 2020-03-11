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

namespace Chevere\Components\Router\Tests;

use Chevere\Components\Http\MethodControllerName;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Exceptions\RouteableException;
use Chevere\Components\Router\Exceptions\RouteNotRouteableException;
use Chevere\Components\Router\Routeable;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RouteableTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(RouteableException::class);
        new Routeable(
            new Route(
                new RouteName('test'),
                new RoutePath('/test')
            )
        );
    }

    public function testConstruct(): void
    {
        $route = (new Route(
            new RouteName('test'),
            new RoutePath('/test')
        ))
            ->withAddedMethodControllerName(
                new MethodControllerName(
                    new GetMethod(),
                    new TestController()
                )
            );
        $routeable = new Routeable($route);
        $this->assertSame($route, $routeable->route());
    }

    public function testNotExportable(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/test'));
        $route->resource = fopen('php://output', 'r');
        $this->expectException(RouteNotRouteableException::class);
        new Routeable($route);
    }
}
