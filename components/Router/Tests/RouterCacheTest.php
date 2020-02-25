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

use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Router\Exceptions\RouterCacheNotFoundException;
use Chevere\Components\Router\Interfaces\RouterCacheInterface;
use Chevere\Components\Router\Interfaces\RouterGroupsInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use Chevere\Components\Router\Interfaces\RouterNamedInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use Chevere\Components\Router\RouteCache;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterCache;
use Chevere\Components\Router\RouterGroups;
use Chevere\Components\Router\RouterIndex;
use Chevere\Components\Router\RouterNamed;
use Chevere\Components\Router\RouterRegex;
use PHPUnit\Framework\TestCase;

final class RouterCacheTest extends TestCase
{
    private CacheHelper $cacheHelper;

    public function setUp(): void
    {
        $this->cacheHelper = new CacheHelper(__DIR__);
    }

    public function testEmptyCache(): void
    {
        $routerCache = new RouterCache($this->cacheHelper->getEmptyCache());
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

    public function testGetEmptyNamed(): void
    {
        $routerCache = new RouterCache($this->cacheHelper->getEmptyCache());
        $this->expectException(RouterCacheNotFoundException::class);
        $routerCache->getNamed();
    }

    public function testGetEmptyGroups(): void
    {
        $routerCache = new RouterCache($this->cacheHelper->getEmptyCache());
        $this->expectException(RouterCacheNotFoundException::class);
        $routerCache->getGroups();
    }

    public function testWorkingCache(): void
    {
        $router = new Router(
            new RouteCache(
                $this->cacheHelper->getWorkingCache()
            )
        );
        $regex = new RouterRegex(
            new Regex('#^(?|/found/([A-z0-9\\_\\-\\%]+) (*:0)|/ (*:1)|/hello-world (*:2))$#x')
        );
        $keys = [
            RouterCacheInterface::KEY_REGEX,
            RouterCacheInterface::KEY_INDEX,
            RouterCacheInterface::KEY_NAMED,
            RouterCacheInterface::KEY_GROUPS
        ];
        $index = (new RouterIndex)->withAdded(new PathUri('/test'), 0, '', '');
        $named = (new RouterNamed)->withAdded('test_name', 1);
        $groups = (new RouterGroups)->withAdded('test_group', 2);
        $router = $router
            ->withRegex($regex)
            ->withIndex($index)
            ->withNamed($named)
            ->withGroups($groups);
        $routerCache = new RouterCache($this->cacheHelper->getWorkingCache());
        $routerCache->put($router);
        $this->assertTrue($routerCache->hasRegex());
        $this->assertTrue($routerCache->hasIndex());
        $this->assertTrue($routerCache->hasNamed());
        $this->assertTrue($routerCache->hasGroups());
        $this->assertInstanceOf(RouterRegexInterface::class, $routerCache->getRegex());
        $this->assertInstanceOf(RouterIndexInterface::class, $routerCache->getIndex());
        $this->assertInstanceOf(RouterNamedInterface::class, $routerCache->getNamed());
        $this->assertInstanceOf(RouterGroupsInterface::class, $routerCache->getGroups());
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $routerCache->puts());
        }
        $routerCache->remove();
        foreach ($keys as $key) {
            $this->assertArrayNotHasKey($key, $routerCache->puts());
        }
    }
}
