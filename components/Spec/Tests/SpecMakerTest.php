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

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Router\Tests\CacheHelper;
use Chevere\Components\Spec\Exceptions\SpecInvalidArgumentException;
use Chevere\Components\Spec\SpecMaker;
use Chevere\Components\Spec\SpecPath;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

final class SpecMakerTest extends TestCase
{
    private CacheHelper $cacheHelper;

    public function setUp(): void
    {
        $this->cacheHelper = new CacheHelper(__DIR__, $this);
    }

    public function tearDown(): void
    {
        $this->cacheHelper->tearDown();
    }

    public function testConstructInvalidArgument(): void
    {
        $shortName = (new ReflectionObject($this))->getShortName();
        $this->expectException(SpecInvalidArgumentException::class);
        new SpecMaker(
            new SpecPath('/spec'),
            new Dir(new Path(__DIR__ . "/_resources/$shortName/spec/")),
            new Router
        );
    }

    public function testConstruct(): void
    {
        $putMethod = new PutMethod;
        $getMethod = new GetMethod;
        $testController = new TestController;
        $route = new Route(new RouteName('route-name'), new RoutePath('/route-path'));
        $route = $route
            ->withAddedEndpoint(
                new RouteEndpoint($putMethod, $testController)
            )
            ->withAddedEndpoint(
                new RouteEndpoint($getMethod, $testController)
            );
        $routerMaker = (new RouterMaker)
            ->withAddedRouteable(
                new Routeable($route),
                'group-name'
            );
        $specMaker = new SpecMaker(
            new SpecPath('/spec'),
            $this->cacheHelper->getWorkingDir()->getChild('spec/'),
            $routerMaker->router()
        );
        $cachedPath = $this->cacheHelper->getCachedDir()->path();
        foreach ($specMaker->files() as $jsonPath => $path) {
            $this->assertFileEquals(
                $cachedPath->getChild(ltrim($jsonPath, '/'))->absolute(),
                $path->absolute()
            );
        }
        $this->assertTrue($specMaker->specIndex()->has(
            $route->name()->toString(),
            $putMethod->name()
        ));
        $this->assertTrue($specMaker->specIndex()->has(
            $route->name()->toString(),
            $getMethod->name()
        ));
    }
}
