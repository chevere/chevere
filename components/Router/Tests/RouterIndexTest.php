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

namespace Chevere\Tests\Router\Properties;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\RouterIndex;
use Chevere\TestApp\App\Controllers\TestController;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RouterIndexTest extends TestCase
{
    public function testConstruct(): void
    {
        $key = '/path';
        $routerIndex = new RouterIndex();
        $this->assertSame([], $routerIndex->toArray());
        $this->assertFalse($routerIndex->hasKey($key));
        $this->expectException(InvalidArgumentException::class);
        $routerIndex->get(404);
    }

    public function testWithAdded(): void
    {
        $key = '/path';
        $id = 0;
        $group = 'some-group';
        $name = 'some-name';
        $routePath = new RoutePath($key);
        $route = new Route(new RouteName($name), $routePath);
        $route = $route->withAddedEndpoint(
            new RouteEndpoint(
                new GetMethod,
                new TestController
            )
        );
        $routeable = new Routeable($route);
        $routerIndex = (new RouterIndex())
            ->withAdded($routeable, $id, $group);
        $this->assertTrue($routerIndex->hasKey($key));
        $this->assertCount(1, $routerIndex->routeIdentifiers());
        $this->assertSame([
            $key => [
                'id' => $id,
                'group' => $group,
                'name' => $name,
            ]
        ], $routerIndex->toArray());
    }
}
