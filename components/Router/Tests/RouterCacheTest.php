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
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Exceptions\RouterCacheNotFoundException;
use Chevere\Components\Router\Interfaces\RouterCacheInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\Routeables;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterCache;
use Chevere\Components\Router\RouterIndex;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Router\RouterRegex;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RouterCacheTest extends TestCase
{
    private CacheHelper $cacheHelper;

    private array $routes;

    public function setUp(): void
    {
        $this->cacheHelper = new CacheHelper(__DIR__, $this);
        $this->routes = [
            new Route(new RouteName('route-1'), new RoutePath('/test')),
            new Route(new RouteName('route-2'), new RoutePath('/test/{id}')),
            new Route(new RouteName('route-3'), new RoutePath('/test/path')),
        ];
    }

    public function tearDown(): void
    {
        $this->cacheHelper->tearDown();
    }

    public function testEmptyCache(): void
    {
        $routerCache = new RouterCache($this->cacheHelper->getEmptyCache());
        $this->assertEmpty($routerCache->puts());
        $this->assertFalse($routerCache->hasRegex());
        $this->assertFalse($routerCache->hasIndex());
        $this->expectException(RouterCacheNotFoundException::class);
        $routerCache->getRegex();
    }

    public function testGetEmptyRegex(): void
    {
        $routerCache = new RouterCache($this->cacheHelper->getEmptyCache());
        $this->expectException(RouterCacheNotFoundException::class);
        $routerCache->getRegex();
    }

    public function testGetEmptyIndex(): void
    {
        $routerCache = new RouterCache($this->cacheHelper->getEmptyCache());
        $this->expectException(RouterCacheNotFoundException::class);
        $routerCache->getIndex();
    }

    public function testWorkingCache(): void
    {
        $router = new Router;
        $regex = new RouterRegex(
            new Regex('#^(?|/found/([A-z0-9\\_\\-\\%]+) (*:0)|/ (*:1)|/hello-world (*:2))$#x')
        );
        $keys = [
            RouterCacheInterface::KEY_REGEX,
            RouterCacheInterface::KEY_INDEX,
        ];
        $route = new Route(new RouteName('test-name'), new RoutePath('/test'));
        $route = $route->withAddedEndpoint(
            new RouteEndpoint(new GetMethod, new TestController)
        );
        $routeable = new Routeable($route);
        $index = (new RouterIndex)->withAdded($routeable, 'test-group');
        $routeable = new Routeable($route);
        $routeables = new Routeables;
        $routeables->put($routeable);
        $router = $router
            ->withRouteables($routeables)
            ->withRegex($regex)
            ->withIndex($index);
        $routerCache = new RouterCache($this->cacheHelper->getWorkingCache());
        $routerCache->put($router);
        $this->assertTrue($routerCache->hasRegex());
        $this->assertTrue($routerCache->hasIndex());
        $this->assertInstanceOf(RouterRegexInterface::class, $routerCache->getRegex());
        $this->assertInstanceOf(RouterIndexInterface::class, $routerCache->getIndex());
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $routerCache->puts());
        }
        $routerCache->remove();
        foreach ($keys as $key) {
            $this->assertArrayNotHasKey($key, $routerCache->puts());
        }
    }

    public function testCachedCache(): void
    {
        $group = 'some-group';
        $cache = $this->cacheHelper->getCachedCache();
        $routerCache = new RouterCache($cache);
        $this->assertTrue($routerCache->hasIndex());
        $this->assertTrue($routerCache->hasRegex());
        $index = $routerCache->getIndex();
        $regex = $routerCache->getRegex();
        $this->assertTrue($index->hasGroup($group));
        foreach ($this->routes as $route) {
            $this->assertStringContainsString(
                str_replace(['/^', '$/'], '', $route->path()->regex()),
                $regex->regex()->toString()
            );
        }
    }

    public function __testGenerateCached(): void
    {
        $this->expectNotToPerformAssertions();
        $group = 'some-group';
        $cache = $this->cacheHelper->getCachedCache();
        $routerCache = new RouterCache($cache);
        $routerMaker = new RouterMaker;
        $routes = $this->routes;
        foreach ($routes as $route) {
            $routerMaker = $routerMaker->withAddedRouteable(
                new Routeable(
                    $route->withAddedEndpoint(
                        new RouteEndpoint(new GetMethod, new TestController)
                    )
                ),
                $group
            );
        }
        $routerCache->put($routerMaker->router());
    }
}
