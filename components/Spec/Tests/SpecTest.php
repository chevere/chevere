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

namespace Chevere\Components\Spec\Tests;

use Chevere\Components\Cache\Cache;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Http\MethodControllerName;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterCache;
use Chevere\Components\Router\RouterGroups;
use Chevere\Components\Router\RouterIndex;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Router\RouterNamed;
use Chevere\Components\Router\RouterRegex;
use Chevere\Components\Router\RoutesCache;
use Chevere\Components\Router\Tests\CacheHelper;
use Chevere\Components\Spec\Exceptions\SpecInvalidArgumentException;
use Chevere\Components\Spec\SpecCache;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class SpecTest extends TestCase
{
    private CacheHelper $cacheHelper;

    public function setUp(): void
    {
        $this->cacheHelper = new CacheHelper(__DIR__);
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

    // public function testInvalidArgument(): void
    // {
    //     $this->expectException(SpecInvalidArgumentException::class);
    //     new SpecCache($this->getEmptyRouter());
    // }

    // public function testConstruct(): void
    // {
    //     $this->expectNotToPerformAssertions();
    //     $spec = new SpecCache(
    //         $this->getEmptyRouter()
    //             ->withGroups(new RouterGroups())
    //             ->withIndex(new RouterIndex())
    //             ->withNamed(new RouterNamed())
    //             ->withRegex(
    //                 new RouterRegex(new Regex('#^(?|/test (*:0))$#x'))
    //             )
    //     );
    // }

    public function testGenerateCached(): void
    {
        $this->expectNotToPerformAssertions();
        $group = 'some-group';
        $cache = new Cache($this->cacheHelper->getWorkingDir());
        $routerCache = new RouterCache($cache);
        $routerMaker = new RouterMaker($routerCache);
        $routes = $this->routes;
        foreach ($routes as $route) {
            $routerMaker = $routerMaker->withAddedRouteable(
                new Routeable(
                    $route->withAddedMethodControllerName(
                        new MethodControllerName(
                            new GetMethod,
                            new TestController
                        )
                    )
                ),
                $group
            );
        }
        // $spec = new SpecCache($routerMaker->router());

        // xdd($spec->toArray());
    }

    private function getEmptyRouter(): RouterInterface
    {
        $dir = new Dir(new Path(__DIR__ . '/_resources/empty/'));
        $cache = new Cache($dir);
        $routesCache = new RoutesCache($cache);

        return new Router($routesCache);
    }

    private function getCachedRouter(): RouterInterface
    {
        $dir = new Dir(new Path(__DIR__ . '/_resources/cached/'));
        $cache = new Cache($dir);
        $routesCache = new RoutesCache($cache);

        return new Router($routesCache);
    }
}
