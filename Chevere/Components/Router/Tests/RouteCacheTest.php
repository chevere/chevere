<?php

namespace Chevere\Components\Router\Tests;

use Chevere\Components\Cache\Cache;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Http\Method;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Route;
use Chevere\Components\Router\Exceptions\RouteCacheTypeException;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\RouteCache;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RouteCacheTest extends TestCase
{
    public function testEmptyCache(): void
    {
        $routeableCache = new RouteCache($this->getEmptyCache());
        $this->assertEmpty($routeableCache->puts());
        $this->assertFalse($routeableCache->has(0));
    }

    public function testWorkingCache(): void
    {
        $routeable = $this->getRouteable();
        $routeableCache = new RouteCache($this->getWorkingCache());
        $id = rand();
        $routeableCache->put($id, $routeable);
        $this->assertTrue($routeableCache->has($id));
        $this->assertEquals($routeable->route(), $routeableCache->get($id));
        $this->assertArrayHasKey($id, $routeableCache->puts());
        $routeableCache->remove($id);
        $this->assertArrayNotHasKey($id, $routeableCache->puts());
    }

    public function testCachedCache(): void
    {
        $id = 0;
        $routeableCache = new RouteCache($this->getCachedCache());
        $this->assertTrue($routeableCache->has($id));
        $this->assertInstanceOf(RouteInterface::class, $routeableCache->get($id));
    }

    public function testCachedCacheTypeError(): void
    {
        $id = 1;
        $routeableCache = new RouteCache($this->getCachedCache());
        $this->assertTrue($routeableCache->has($id));
        $this->expectException(RouteCacheTypeException::class);
        $routeableCache->get($id);
    }

    private function getResourcesChildDir(string $child): DirInterface
    {
        return new Dir(
            (new Path(__DIR__))->getChild('_resources')->getChild($child)
        );
    }
    private function getEmptyCache(): CacheInterface
    {
        return new Cache(
            $this->getResourcesChildDir('empty')
        );
    }

    private function getWorkingCache(): CacheInterface
    {
        return new Cache(
            $this->getResourcesChildDir('working')
        );
    }

    private function getCachedCache(): CacheInterface
    {
        return new Cache(
            $this->getResourcesChildDir('cached')
        );
    }

    private function getRouteable(): RouteableInterface
    {
        $route = new Route(new PathUri('/test'));
        $route = $route->withAddedMethod(
            new Method('GET'),
            new ControllerName(TestController::class)
        );

        return new Routeable($route);
    }
}
