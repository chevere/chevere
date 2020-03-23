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

namespace Chevere\Components\Route\Tests;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteEndpointsMap;
use Chevere\TestApp\App\Controllers\TestController;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class RouteEndpointsMapTest extends TestCase
{
    public function testConstruct(): void
    {
        $method = new GetMethod;
        $routeEndpointsMap = new RouteEndpointsMap;
        $this->assertCount(0, $routeEndpointsMap->map());
        $this->assertFalse($routeEndpointsMap->hasKey($method));
        $this->expectException(OutOfBoundsException::class);
        $routeEndpointsMap->get($method);
    }

    public function testPut(): void
    {
        $method = new GetMethod;
        $routeEndpointsMap = new RouteEndpointsMap;
        $routeEndpointsMap->put(new RouteEndpoint($method, new TestController));
        $this->assertTrue($routeEndpointsMap->hasKey($method));
        $this->assertTrue($routeEndpointsMap->hasKey(new GetMethod));
    }
}
