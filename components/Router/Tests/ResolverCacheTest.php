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

use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Exceptions\RouteCacheNotFoundException;
use Chevere\Components\Router\Exceptions\RouteCacheTypeException;
use Chevere\Components\Router\ResolverCache;
use Chevere\Components\Router\RouteResolve;
use PHPUnit\Framework\TestCase;

final class ResolverCacheTest extends TestCase
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
        $resolverCache = new ResolverCache($this->cacheHelper->getEmptyCache());
        /** @var int $id */
        $keys = array_keys($this->routes);
        foreach ($keys as $id) {
            $this->assertFalse($resolverCache->has($id));
        }
        $this->assertEmpty($resolverCache->puts());
        $this->expectException(RouteCacheNotFoundException::class);
        $resolverCache->get($keys[0]);
    }

    public function testWorkingCache(): void
    {
        $resolverCache = new ResolverCache($this->cacheHelper->getWorkingCache());
        /**
         * @var int $pos
         * @var Route $route
         */
        foreach ($this->routes as $pos => $route) {
            $routeResolve = new RouteResolve(
                $route->name()->toString(),
                $route->path()->wildcards()
            );
            $resolverCache->put($pos, $routeResolve);
            $this->assertArrayHasKey($pos, $resolverCache->puts());
            $this->assertEquals(
                $routeResolve,
                $resolverCache->get(/** @scrutinizer ignore-type */$pos)
            );
            $resolverCache->remove($pos);
            $this->assertArrayNotHasKey($pos, $resolverCache->puts());
        }
    }

    public function testCachedCache(): void
    {
        $resolverCache = new ResolverCache($this->cacheHelper->getCachedCache());
        /**
         * @var int $pos
         * @var Route $route
         */
        foreach ($this->routes as $pos => $route) {
            $this->assertTrue($resolverCache->has($pos));
            $routeResolve = new RouteResolve(
                $route->name()->toString(),
                $route->path()->wildcards()
            );
            $this->assertEquals($routeResolve, $resolverCache->get($pos));
        }
    }

    public function testWrongCachedCache(): void
    {
        $pos = 0;
        $resolverCache = new ResolverCache($this->cacheHelper->getWrongCache());
        $this->assertTrue($resolverCache->has($pos));
        $this->expectException(RouteCacheTypeException::class);
        $resolverCache->get($pos);
    }

    public function _testGenerateCached(): void
    {
        $this->expectNotToPerformAssertions();
        $resolverCache = new ResolverCache($this->cacheHelper->getCachedCache());
        /**
         * @var int $pos
         * @var Route $route
         */
        foreach ($this->routes as $pos => $route) {
            $routeResolve = new RouteResolve(
                $route->name()->toString(),
                $route->path()->wildcards()
            );
            $resolverCache->put($pos, $routeResolve);
        }
    }
}
