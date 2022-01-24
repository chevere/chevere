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

use function Chevere\Filesystem\dirForPath;
use Chevere\Http\Methods\GetMethod;
use Chevere\Http\Methods\PutMethod;
use Chevere\Router\Route\RouteEndpoint;
use Chevere\Router\Route\RouteLocator;
use Chevere\Spec\SpecIndex;
use Chevere\Spec\Specs\RouteEndpointSpec;
use Chevere\Tests\Spec\_resources\src\TestController;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class SpecIndexTest extends TestCase
{
    public function testConstruct(): void
    {
        $specIndex = new SpecIndex();
        $method = new GetMethod();
        $this->assertFalse($specIndex->has('404', $method::name()));
        $this->assertCount(0, $specIndex);
        $this->expectException(OutOfBoundsException::class);
        $specIndex->get('404', $method::name());
    }

    public function testWithOffset(): void
    {
        $routeLocator = new RouteLocator('repo', '/path');
        $getMethod = new GetMethod();
        $routeEndpoint = new RouteEndpoint($getMethod, new TestController());
        $specDir = dirForPath('/spec/group/route/');
        $routeEndpointSpec = new RouteEndpointSpec($specDir, $routeEndpoint);
        $specIndex = (new SpecIndex())->withAddedRoute(
            $routeLocator->__toString(),
            $routeEndpointSpec
        );
        $this->assertFalse($specIndex->has('404', $getMethod->name()));
        $this->assertTrue($specIndex->has(
            $routeLocator->__toString(),
            $getMethod->name()
        ));
        $this->assertCount(1, $specIndex);
        $this->assertSame(
            $specDir->path()->__toString() . $getMethod->name() . '.json',
            $specIndex->get(
                $routeLocator->__toString(),
                $getMethod->name()
            )
        );
        $method2 = new PutMethod();
        $routeEndpoint2 = new RouteEndpoint($method2, new TestController());
        $routeEndpointSpec2 = new RouteEndpointSpec($specDir, $routeEndpoint2);
        $specIndex = $specIndex->withAddedRoute(
            $routeLocator->__toString(),
            $routeEndpointSpec2
        );
        $this->assertTrue($specIndex->has(
            $routeLocator->__toString(),
            $getMethod->name()
        ));
        $this->assertTrue($specIndex->has(
            $routeLocator->__toString(),
            $method2->name()
        ));
        $this->assertCount(1, $specIndex);
        $this->assertSame(
            $specDir->path()->__toString() . $method2->name() . '.json',
            $specIndex->get($routeLocator->__toString(), $method2->name())
        );
    }
}
