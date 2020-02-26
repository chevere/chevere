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

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Router\Exceptions\RouteKeyConflictException;
use Chevere\Components\Router\Exceptions\RouteNameConflictException;
use Chevere\Components\Router\Exceptions\RoutePathExistsException;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Router\Interfaces\RouterCacheInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
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
        foreach (array_keys($this->routerCache->routeCache()->puts()) as $pos) {
            $this->routerCache->routeCache()->remove($pos);
        }
    }

    public function testConstruct(): void
    {
        $routerMaker = new RouterMaker($this->routerCache);
        $this->assertInstanceOf(RouterInterface::class, $routerMaker->router());
    }

    // public function testWithAddedRouteable(): void
    // {
    //     $routeable1 = $this->getRouteable('/path-1', 'PathName-1');
    //     $routeable2 = $this->getRouteable('/path-2', 'PathName-2');
    //     $pathUri = $routeable1->route()->pathUri();
    //     $routerMaker = (new RouterMaker($this->routerCache))
    //         ->withAddedRouteable($routeable1, 'group')
    //         ->withAddedRouteable($routeable2, 'group');
    //     $this->assertTrue($routerMaker->router()->index()->has($pathUri));
    // }

    // public function testWithAlreadyAddedPath(): void
    // {
    //     $routeable = $this->getRouteable('/path', 'PathName');
    //     $this->expectException(RoutePathExistsException::class);
    //     (new RouterMaker($this->routerCache))
    //         ->withAddedRouteable($routeable, 'group')
    //         ->withAddedRouteable($routeable, 'another-group');
    // }

    // public function testWithAlreadyAddedKey(): void
    // {
    //     $routeable1 = $this->getRouteable('/path/{foo}', 'FooName');
    //     $routeable2 = $this->getRouteable('/path/{bar}', 'BarName');
    //     $this->expectException(RouteKeyConflictException::class);
    //     (new RouterMaker($this->routerCache))
    //         ->withAddedRouteable($routeable1, 'group')
    //         ->withAddedRouteable($routeable2, 'another-group');
    // }

    // public function testWithAlreadyAddedName(): void
    // {
    //     $routeable1 = $this->getRouteable('/path1', 'SomeName');
    //     $routeable2 = $this->getRouteable('/path2', 'SomeName');
    //     $routeable3 = $this->getRouteable('/path3', 'SameName');
    //     $this->expectException(RouteNameConflictException::class);
    //     (new RouterMaker($this->routerCache))
    //         ->withAddedRouteable($routeable1, 'group1')
    //         ->withAddedRouteable($routeable2, 'group2')
    //         ->withAddedRouteable($routeable3, 'group3');
    // }

    private function getRouteable(string $path, string $name = null): RouteableInterface
    {
        $route = new Route(new PathUri($path));
        $route = $route
            ->withAddedMethod(
                new GetMethod(),
                new ControllerName(TestController::class)
            );
        if ($name !== null) {
            $route = $route->withName(new RouteName($name));
        }

        return new Routeable($route);
    }
}
