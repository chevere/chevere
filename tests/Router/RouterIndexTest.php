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
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\RouterIndex;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Router\RouteIdentifierInterface;
use Chevere\Tests\Router\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RouterIndexTest extends TestCase
{
    public function testConstruct(): void
    {
        $routerIndex = new RouterIndex();
        $this->assertSame([], $routerIndex->toArray());
    }

    public function testGetRouteIdentifier(): void
    {
        $routerIndex = new RouterIndex();
        $this->expectException(OutOfBoundsException::class);
        $routerIndex->getRouteIdentifier('not-found');
    }

    public function testGetGroupRouteNames(): void
    {
        $routerIndex = new RouterIndex();
        $this->expectException(OutOfBoundsException::class);
        $routerIndex->getGroupRouteNames('not-found');
    }

    public function testGetRouteGroup(): void
    {
        $routerIndex = new RouterIndex();
        $this->expectException(OutOfBoundsException::class);
        $routerIndex->getRouteGroup('not-found');
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
        $routerIndex = (new RouterIndex)->withAddedRoutable($routable, $groupName);
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
        $routerIndex = $routerIndex->withAddedRoutable($routable2, $groupName);
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
        $routerIndex = (new RouterIndex())->withAddedRoutable($routable, $group);
        $this->expectException(OverflowException::class);
        $routerIndex->withAddedRoutable($routable, 'other-group');
    }
}
