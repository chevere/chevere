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

use Chevere\Components\Router\Exceptions\RouterCacheNotFoundException;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterCache;
use PHPUnit\Framework\TestCase;

final class RouterCacheTest extends TestCase
{
    public function setUp(): void
    {
        $this->helper = new CacheHelper(__DIR__);
    }

    public function testEmptyCache(): void
    {
        $routerCache = new RouterCache($this->helper->getEmptyCache());
        $this->assertEmpty($routerCache->puts());
        $this->assertFalse($routerCache->hasRegex());
        $this->assertFalse($routerCache->hasIndex());
        $this->assertFalse($routerCache->hasNamed());
        $this->assertFalse($routerCache->hasGroups());
        $this->expectException(RouterCacheNotFoundException::class);
        $routerCache->getRegex();
    }

    public function testGetEmptyRegex(): void
    {
        $routerCache = new RouterCache($this->helper->getEmptyCache());
        $this->expectException(RouterCacheNotFoundException::class);
        $routerCache->getRegex();
    }

    public function testGetEmptyIndex(): void
    {
        $routerCache = new RouterCache($this->helper->getEmptyCache());
        $this->expectException(RouterCacheNotFoundException::class);
        $routerCache->getIndex();
    }

    public function testGetEmptyNamed(): void
    {
        $routerCache = new RouterCache($this->helper->getEmptyCache());
        $this->expectException(RouterCacheNotFoundException::class);
        $routerCache->getNamed();
    }

    public function testGetEmptyGroups(): void
    {
        $routerCache = new RouterCache($this->helper->getEmptyCache());
        $this->expectException(RouterCacheNotFoundException::class);
        $routerCache->getGroups();
    }

    // public function testWorkingCache(): void
    // {
    //     // $router = new Router();
    //     // // $router = $router->withRegex();

    //     // $routeableCache = new RouteCache($this->helper->getWorkingCache());
    //     // $id = rand();
    //     // $routeableCache->put($router);
    //     // $this->assertTrue($routeableCache->has($id));
    //     // $this->assertArrayHasKey($id, $routeableCache->puts());
    //     // $routeableCache->remove();
    //     // $this->assertArrayNotHasKey($id, $routeableCache->puts());
    // }
}
