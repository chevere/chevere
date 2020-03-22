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
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Exceptions\RouteCacheNotFoundException;
use Chevere\Components\Router\Exceptions\RouteCacheTypeException;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\RoutesCache;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RoutesCacheTest extends TestCase
{
    private RouteNameInterface $routeName;

    private CacheHelper $cacheHelper;

    public function setUp(): void
    {
        $this->routeName = new RouteName('route-name');
        $this->cacheHelper = new CacheHelper(__DIR__);
    }

    public function tearDown(): void
    {
        $this->cacheHelper->tearDown();
    }

    public function testEmptyCache(): void
    {
        $routeableCache = new RoutesCache($this->cacheHelper->getEmptyCache());
        $this->assertEmpty($routeableCache->puts());
        $this->assertFalse($routeableCache->has($this->routeName->toString()));
        $this->expectException(RouteCacheNotFoundException::class);
        $this->assertFalse($routeableCache->get($this->routeName->toString()));
    }

    public function testWorkingCache(): void
    {
        $routeable = $this->getRouteable();
        $routesCache = new RoutesCache($this->cacheHelper->getWorkingCache());
        $routeName = $routeable->route()->name()->toString();
        $routesCache->put($routeable);
        $this->assertTrue($routesCache->has($routeName));
        $this->assertEquals($routeable->route(), $routesCache->get($routeName));
        $this->assertArrayHasKey($routeName, $routesCache->puts());
        $routesCache->remove($routeName);
        $this->assertArrayNotHasKey($routeName, $routesCache->puts());
    }

    public function testCachedCache(): void
    {
        $routesCache = new RoutesCache(
            $this->cacheHelper->getCachedCache()->getChild('routes/')
        );
        $this->assertTrue($routesCache->has($this->routeName->toString()));
        $this->assertInstanceOf(
            RouteInterface::class,
            $routesCache->get($this->routeName->toString())
        );
    }

    public function testCachedCacheTypeError(): void
    {
        $id = 'wrong';
        $routesCache = new RoutesCache(
            $this->cacheHelper->getCachedCache()->getChild('wrong-type/')
        );
        $this->assertTrue($routesCache->has($id));
        $this->expectException(RouteCacheTypeException::class);
        $routesCache->get($id);
    }

    private function getRouteable(): RouteableInterface
    {
        $route = new Route(
            $this->routeName,
            new RoutePath('/test/{var}')
        );
        $route = $route
            ->withAddedEndpoint(
                new RouteEndpoint(
                    new GetMethod(),
                    new TestController
                )
            );

        return new Routeable($route);
    }
}
