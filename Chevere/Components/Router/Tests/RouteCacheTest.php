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
use Chevere\Components\Router\Exceptions\RouteCacheNotFoundException;
use Chevere\Components\Router\Exceptions\RouteCacheTypeException;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\RouteCache;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RouteCacheTest extends TestCase
{
    private CacheHelper $helper;

    public function setUp(): void
    {
        $this->helper = new CacheHelper(__DIR__);
    }

    public function testEmptyCache(): void
    {
        $routeableCache = new RouteCache($this->helper->getEmptyCache());
        $this->assertEmpty($routeableCache->puts());
        $this->assertFalse($routeableCache->has(0));
        $this->expectException(RouteCacheNotFoundException::class);
        $this->assertFalse($routeableCache->get(0));
    }

    public function testWorkingCache(): void
    {
        $routeable = $this->getRouteable();
        $routeableCache = new RouteCache($this->helper->getWorkingCache());
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
        $routeableCache = new RouteCache($this->helper->getCachedCache());
        $this->assertTrue($routeableCache->has($id));
        $this->assertInstanceOf(RouteInterface::class, $routeableCache->get($id));
    }

    public function testCachedCacheTypeError(): void
    {
        $id = 1;
        $routeableCache = new RouteCache($this->helper->getCachedCache());
        $this->assertTrue($routeableCache->has($id));
        $this->expectException(RouteCacheTypeException::class);
        $routeableCache->get($id);
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
