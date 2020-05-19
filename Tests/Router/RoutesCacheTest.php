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

namespace Chevere\Tests\Router;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\ControllerResponse;
use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use Chevere\Interfaces\Controller\ControllerResponseInterface;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Regex\Regex;
use Chevere\Interfaces\Route\RouteInterface;
use Chevere\Interfaces\Route\RouteNameInterface;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Exceptions\RouteCacheNotFoundException;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\RoutesCache;
use PHPUnit\Framework\TestCase;
use TypeError;

final class RoutesCacheTest extends TestCase
{
    private RouteNameInterface $routeName;

    private CacheHelper $cacheHelper;

    public function setUp(): void
    {
        $this->routeName = new RouteName('route-name');
        $this->cacheHelper = new CacheHelper(__DIR__, $this);
    }

    public function tearDown(): void
    {
        $this->cacheHelper->tearDown();
    }

    public function testEmptyCache(): void
    {
        $routableCache = new RoutesCache($this->cacheHelper->getEmptyCache());
        $this->assertEmpty($routableCache->puts());
        $this->assertFalse($routableCache->has($this->routeName->toString()));
        $this->expectException(RouteCacheNotFoundException::class);
        $this->assertFalse($routableCache->get($this->routeName->toString()));
    }

    public function testWorkingCache(): void
    {
        $routable = $this->getRoutable();
        $routesCache = new RoutesCache($this->cacheHelper->getWorkingCache());
        $routeName = $routable->route()->name()->toString();
        $routesCache->put($routable->route());
        $this->assertTrue($routesCache->has($routeName));
        $this->assertEquals($routable->route(), $routesCache->get($routeName));
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
        $this->expectException(TypeError::class);
        $routesCache->get($id);
    }

    public function _testGenerateCachedRoute(): void
    {
        $this->expectNotToPerformAssertions();
        $routable = $this->getRoutable();
        $routesCache = new RoutesCache(
            $this->cacheHelper->getCachedCache()->getChild('routes/')
        );
        $routesCache->put($routable->route());
    }

    private function getRoutable(): RoutableInterface
    {
        $route = new Route($this->routeName, new RoutePath('/test/{name}'));
        $route = $route
            ->withAddedEndpoint(
                new RouteEndpoint(new GetMethod, new RoutesCacheTestController)
            );

        return new Routable($route);
    }
}

final class RoutesCacheTestController extends Controller
{
    public function getParameters(): ControllerParametersInterface
    {
        return (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('name', new Regex('/^[\w]+$/'))
            );
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponse(true);
    }
}
