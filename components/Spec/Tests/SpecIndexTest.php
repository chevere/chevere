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

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Spec\RouteEndpointSpec;
use Chevere\Components\Spec\SpecIndex;
use Chevere\Components\Spec\SpecPath;
use Chevere\TestApp\App\Controllers\TestController;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class SpecIndexTest extends TestCase
{
    public function testConstruct(): void
    {
        $specIndex = new SpecIndex;
        $method = new GetMethod;
        $this->assertCount(0, $specIndex->specIndexMap()->map());
        $this->assertFalse($specIndex->specIndexMap()->hasKey(0));
        $this->expectException(OutOfBoundsException::class);
        $specIndex->get(0, $method);
    }

    public function testWithOffset(): void
    {
        $method = new GetMethod;
        $routeEndpoint = new RouteEndpoint($method, new TestController);
        $specPath = new SpecPath('/spec/group/route');
        $routeEndpointSpec = new RouteEndpointSpec($specPath, $routeEndpoint);
        $specIndex = (new SpecIndex)->withOffset(1, $routeEndpointSpec);
        $specIndexMap = $specIndex->specIndexMap();
        $specMethods = $specIndexMap->map()->get(1);
        $this->assertCount(1, $specIndexMap->map());
        $this->assertFalse($specIndexMap->map()->hasKey(0));
        $this->assertTrue($specIndexMap->map()->hasKey(1));
        $this->assertTrue($specMethods->hasKey($method));
        $this->assertSame(
            $specPath->getChild($method->name() . '.json')->pub(),
            $specIndex->get(1, $method)
        );
        //
        $method2 = new PutMethod;
        $routeEndpoint2 = new RouteEndpoint($method2, new TestController);
        $routeEndpointSpec2 = new RouteEndpointSpec($specPath, $routeEndpoint2);
        $specIndex = $specIndex->withOffset(1, $routeEndpointSpec2);
        $specIndexMap = $specIndex->specIndexMap();
        $this->assertTrue($specIndexMap->map()->hasKey(1));
        $specMethods = $specIndexMap->map()->get(1);
        $this->assertTrue($specMethods->hasKey($method));
        $this->assertTrue($specMethods->hasKey($method2));
        $this->assertSame(
            $specPath->getChild($method2->name() . '.json')->pub(),
            $specIndex->get(1, $method2)
        );
    }
}
