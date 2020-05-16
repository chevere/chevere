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
use Chevere\Components\Router\Interfaces\RouteIdentifierInterface;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\RouterIndex;
use Chevere\TestApp\App\Controllers\TestController;
use LogicException;
use PHPUnit\Framework\TestCase;

final class RouterIndexTest extends TestCase
{
    public function testConstruct(): void
    {
        $routerIndex = new RouterIndex();
        $this->assertSame([], $routerIndex->toArray());
    }

    public function testWithAdded(): void
    {
        $groupName = 'some-group';
        $routeName = 'some-name';
        $route = new Route(new RouteName($routeName), new RoutePath('/path'));
        $route = $route->withAddedEndpoint(
            new RouteEndpoint(new GetMethod, new TestController)
        );
        $routable = new Routable($route);
        $routerIndex = (new RouterIndex)->withAdded($routable, $groupName);
        $this->assertTrue($routerIndex->hasRouteName($routeName));
        $this->assertInstanceOf(
            RouteIdentifierInterface::class,
            $routerIndex->getRouteIdentifier($routeName)
        );
        $this->assertTrue($routerIndex->hasGroup($groupName));
        $this->assertSame([$routeName], $routerIndex->getGroupRouteNames($groupName));
        $this->assertSame($groupName, $routerIndex->getRouteGroup($routeName));
        $this->assertSame([
            $routeName => [
                'group' => $groupName,
                'name' => $routeName,
            ]
        ], $routerIndex->toArray());
        $routeName2 = 'route-name-2';
        $route2 = new Route(new RouteName($routeName2), new RoutePath('/path-2'));
        $route2 = $route2->withAddedEndpoint(
            new RouteEndpoint(new GetMethod, new TestController)
        );
        $routable2 = new Routable($route2);
        $routerIndex = $routerIndex->withAdded($routable2, $groupName);
        $this->assertSame([$routeName, $routeName2], $routerIndex->getGroupRouteNames($groupName));
    }

    public function testWithAddedAlready(): void
    {
        $group = 'group-name';
        $route = (new Route(new RouteName('route-name'), new RoutePath('/path')))
            ->withAddedEndpoint(
                new RouteEndpoint(new GetMethod, new TestController)
            );
        $routable = new Routable($route);
        $routerIndex = (new RouterIndex())->withAdded($routable, $group);
        $this->expectException(LogicException::class);
        $routerIndex->withAdded($routable, 'other-group');
    }
}
