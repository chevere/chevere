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

use Chevere\Components\Cache\Exceptions\CacheKeyNotFoundException;
use Chevere\Components\Http\Methods\ConnectMethod;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Router\Tests\CacheHelper;
use Chevere\Components\Spec\SpecIndex;
use Chevere\Components\Spec\SpecIndexCache;
use Chevere\Components\Spec\SpecPath;
use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class SpecIndexCacheTest extends TestCase
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

    public function testEmptyCache(): void
    {
        $specIndexCache = new SpecIndexCache($this->cacheHelper->getEmptyCache());
        $this->assertFalse($specIndexCache->has('404'));
        $this->expectException(CacheKeyNotFoundException::class);
        $this->assertFalse($specIndexCache->get('404'));
    }

    public function testWorkingCache(): void
    {
        $routeName = new RouteName('route-name');
        $method = new ConnectMethod;
        $specPath = new SpecPath('/spec/group/route');
        $specIndexCache = new SpecIndexCache($this->cacheHelper->getWorkingCache());
        $specIndexCache->put(
            (new SpecIndex)->withOffset(
                $routeName->toString(),
                new RouteEndpointSpec(
                    $specPath,
                    new RouteEndpoint($method, new TestController)
                )
            )
        );
        $this->assertIsArray($specIndexCache->puts()[$routeName->toString()]);
        $this->assertTrue($specIndexCache->has($routeName->toString()));
        $specMethods = $specIndexCache->get($routeName->toString());
        $this->assertTrue($specMethods->hasKey($method->name()));
        $this->assertSame(
            $specPath->getChild($method->name() . '.json')->pub(),
            $specMethods->get($method->name())
        );
    }

    public function testCachedCache(): void
    {
        $routeName = new RouteName('route-name');
        $method = new ConnectMethod;
        $specPath = new SpecPath('/spec/group/route');
        $specIndexCache = new SpecIndexCache($this->cacheHelper->getCachedCache());
        $this->assertTrue($specIndexCache->has($routeName->toString()));
        $specMethods = $specIndexCache->get($routeName->toString());
        $this->assertTrue($specMethods->hasKey($method->name()));
        $this->assertSame(
            $specPath->getChild($method->name() . '.json')->pub(),
            $specMethods->get($method->name())
        );
    }
}
