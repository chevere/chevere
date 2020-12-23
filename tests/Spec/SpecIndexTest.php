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

use function Chevere\Components\Filesystem\dirForPath;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RouteLocator;
use Chevere\Components\Spec\SpecIndex;
use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Tests\Spec\_resources\src\TestController;
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
            $routeLocator->toString(),
            $routeEndpointSpec
        );
        $this->assertFalse($specIndex->has('404', $getMethod->name()));
        $this->assertTrue($specIndex->has(
            $routeLocator->toString(),
            $getMethod->name()
        ));
        $this->assertCount(1, $specIndex);
        $this->assertSame(
            $specDir->path()->toString() . $getMethod->name() . '.json',
            $specIndex->get(
                $routeLocator->toString(),
                $getMethod->name()
            )
        );
        $method2 = new PutMethod();
        $routeEndpoint2 = new RouteEndpoint($method2, new TestController());
        $routeEndpointSpec2 = new RouteEndpointSpec($specDir, $routeEndpoint2);
        $specIndex = $specIndex->withAddedRoute(
            $routeLocator->toString(),
            $routeEndpointSpec2
        );
        $this->assertTrue($specIndex->has(
            $routeLocator->toString(),
            $getMethod->name()
        ));
        $this->assertTrue($specIndex->has(
            $routeLocator->toString(),
            $method2->name()
        ));
        $this->assertCount(1, $specIndex);
        $this->assertSame(
            $specDir->path()->toString() . $method2->name() . '.json',
            $specIndex->get($routeLocator->toString(), $method2->name())
        );
    }
}
