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

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Exceptions\RouteKeyConflictException;
use Chevere\Components\Router\Exceptions\RouteNameConflictException;
use Chevere\Components\Router\Exceptions\RoutePathExistsException;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Router\Interfaces\RouterCacheInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RoutesCacheInterface;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\RouterCache;
use Chevere\Components\Router\RouterMaker;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RouterMakerTest extends TestCase
{
    private CacheHelper $cacheHelper;

    private RouterCacheInterface $routerCache;

    public function setUp(): void
    {
        $this->cacheHelper = new CacheHelper(__DIR__);
        $this->routerCache = new RouterCache($this->cacheHelper->getWorkingCache());
    }

    public function tearDown(): void
    {
        $this->cacheHelper->tearDown();
        $this->routerCache->remove();
        foreach (array_keys($this->routerCache->routesCache()->puts()) as $routeName) {
            $this->routerCache->routesCache()->remove($routeName);
        }
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(
            RouterInterface::class,
            (new RouterMaker)->router()
        );
    }

    public function testWithAddedRouteable(): void
    {
        $routeable1 = $this->getRouteable('/path-1', 'route-name-1');
        $routeable2 = $this->getRouteable('/path-2', 'route-name-2');
        $routerMaker = (new RouterMaker)
            ->withAddedRouteable($routeable1, 'group')
            ->withAddedRouteable($routeable2, 'group');
        $this->assertCount(2, $routerMaker->router()->routeables()->map());
        $routerIndex = $routerMaker->router()->index();
        $this->assertTrue($routerIndex->hasRouteName(
            $routeable1->route()->name()->toString()
        ));
        $this->assertTrue($routerIndex->hasRouteName(
            $routeable2->route()->name()->toString()
        ));
    }

    public function testWithAlreadyAddedPath(): void
    {
        $this->expectException(RoutePathExistsException::class);
        (new RouterMaker)
            ->withAddedRouteable(
                $this->getRouteable('/path', 'route-name'),
                'group'
            )
            ->withAddedRouteable(
                $this->getRouteable('/path', 'route-name2'),
                'another-group'
            );
    }

    public function testWithAlreadyAddedKey(): void
    {
        $routeable1 = $this->getRouteable('/path/{foo}', 'FooName');
        $routeable2 = $this->getRouteable('/path/{bar}', 'BarName');
        $this->expectException(RouteKeyConflictException::class);
        (new RouterMaker)
            ->withAddedRouteable($routeable1, 'group')
            ->withAddedRouteable($routeable2, 'another-group');
    }

    public function testWithAlreadyAddedName(): void
    {
        $routeable1 = $this->getRouteable('/path1', 'SomeName');
        $routeable2 = $this->getRouteable('/path2', 'SomeName');
        $routeable3 = $this->getRouteable('/path3', 'SameName');
        $this->expectException(RouteNameConflictException::class);
        (new RouterMaker)
            ->withAddedRouteable($routeable1, 'group1')
            ->withAddedRouteable($routeable2, 'group2')
            ->withAddedRouteable($routeable3, 'group3');
    }

    private function getRouteable(string $routePath, string $routeName): RouteableInterface
    {
        $route = new Route(new RouteName($routeName), new RoutePath($routePath));
        $route = $route
            ->withAddedEndpoint(
                new RouteEndpoint(
                    new GetMethod,
                    new TestController
                )
            );

        return new Routeable($route);
    }
}
