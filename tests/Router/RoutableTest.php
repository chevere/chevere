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
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Exceptions\Router\RouteNotRoutableException;
use Chevere\Exceptions\Router\RouteWithoutEndpointsException;
use Chevere\Tests\Router\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RoutableTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $route = new Route(new RoutePath('/test'));
        $this->expectException(RouteWithoutEndpointsException::class);
        new Routable($route);
    }

    public function testConstruct(): void
    {
        $route = (new Route(new RoutePath('/test')))
            ->withAddedEndpoint(
                new RouteEndpoint(
                    new GetMethod(),
                    new TestController()
                )
            );
        $routable = new Routable($route);
        $this->assertSame($route, $routable->route());
    }

    public function testNotExportable(): void
    {
        $route = new Route(new RoutePath('/test'));
        $route->resource = fopen('php://output', 'r+');
        $this->expectException(RouteNotRoutableException::class);
        new Routable($route);
    }
}
