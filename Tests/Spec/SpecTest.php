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

namespace Chevere\Tests\Spec;

use Chevere\Components\Cache\Cache;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\RouterMaker;
use Chevere\Tests\Router\CacheHelper;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class SpecTest extends TestCase
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
        // $routerCache = new RouterCache($cache);
        $routerMaker = new RouterMaker;
        $routes = $this->routes;
        /** @var RouteInterface $route */
        foreach ($routes as $route) {
            $routerMaker = $routerMaker->withAddedRoutable(
                new Routable(
                    $route->withAddedEndpoint(
                        new RouteEndpoint(
                            new GetMethod,
                            new TestController
                        )
                    )
                ),
                $group
            );
        }
    }
}
