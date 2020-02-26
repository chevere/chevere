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

use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Route;
use Chevere\Components\Router\Exceptions\RouteNotFoundException;
use Chevere\Components\Router\Exceptions\RouterException;
use Chevere\Components\Router\Interfaces\RouteCacheInterface;
use Chevere\Components\Router\Interfaces\RoutedInterface;
use Chevere\Components\Router\RouteCache;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterGroups;
use Chevere\Components\Router\RouterIndex;
use Chevere\Components\Router\RouterNamed;
use Chevere\Components\Router\RouterRegex;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private CacheHelper $cacheHelper;

    public function setUp(): void
    {
        $this->cacheHelper = new CacheHelper(__DIR__);
    }

    public function tearDown(): void
    {
        $this->cacheHelper->tearDown();
    }

    public function testConstructor(): void
    {
        $router = new Router($this->getEmptyRouteCache());
        $this->assertFalse($router->hasRegex());
        $this->assertFalse($router->hasIndex());
        $this->assertFalse($router->hasNamed());
        $this->assertFalse($router->hasGroups());
        $this->assertFalse($router->canResolve());
    }

    public function testUnableToResolveException(): void
    {
        $router = new Router($this->getEmptyRouteCache());
        $this->expectException(RouterException::class);
        $router->resolve(new Uri('/'));
    }

    public function testRegexNotFound(): void
    {
        $regex = new RouterRegex(
            new Regex('#^(?|/test (*:0))$#x')
        );
        $router = (new Router($this->getEmptyRouteCache()))->withRegex($regex);
        $this->assertTrue($router->hasRegex());
        $this->assertSame($regex, $router->regex());
        $this->assertTrue($router->canResolve());
        $this->expectException(RouteNotFoundException::class);
        $router->resolve(new Uri('/not-found'));
    }

    // public function testRegexFound(): void
    // {
    //     $regex = new RouterRegex(
    //         new Regex('#^(?|/found/([A-z0-9\\_\\-\\%]+) (*:0)|/ (*:1)|/hello-world (*:2))$#x')
    //     );
    //     $router = (new Router($this->getCachedRouteCache()))->withRegex($regex);
    //     $this->assertTrue($router->hasRegex());
    //     $this->assertSame($regex, $router->regex());
    //     $this->assertTrue($router->canResolve());
    //     $routed = $router->resolve(new Uri('/found/yay'));
    //     $this->assertInstanceOf(RoutedInterface::class, $routed);
    // }

    public function testIndex(): void
    {
        $index = (new RouterIndex)->withAdded(new PathUri('/test'), 0, '', '');
        $router = (new Router($this->getEmptyRouteCache()))->withIndex($index);
        $this->assertTrue($router->hasIndex());
        $this->assertSame($index, $router->index());
    }

    public function testNamed(): void
    {
        $named = (new RouterNamed)->withAdded('test_name', 1);
        $router = (new Router($this->getEmptyRouteCache()))->withNamed($named);
        $this->assertTrue($router->hasNamed());
        $this->assertSame($named, $router->named());
    }

    public function testGroups(): void
    {
        $groups = (new RouterGroups)->withAdded('test_group', 2);
        $router = (new Router($this->getEmptyRouteCache()))->withGroups($groups);
        $this->assertTrue($router->hasGroups());
        $this->assertSame($groups, $router->groups());
    }

    private function getEmptyRouteCache(): RouteCacheInterface
    {
        return new RouteCache($this->cacheHelper->getEmptyCache());
    }

    private function getCachedRouteCache(): RouteCacheInterface
    {
        return new RouteCache($this->cacheHelper->getCachedCache());
    }
}
