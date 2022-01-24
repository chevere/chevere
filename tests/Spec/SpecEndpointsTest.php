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
use Chevere\Router\Route\RouteEndpoint;
use Chevere\Spec\SpecEndpoints;
use Chevere\Spec\Specs\RouteEndpointSpec;
use Chevere\Tests\Router\Route\_resources\src\TestController;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class SpecEndpointsTest extends TestCase
{
    public function testConstruct(): void
    {
        $specEndpoints = new SpecEndpoints();
        $this->assertCount(0, $specEndpoints);
        $this->assertFalse($specEndpoints->has('not-found'));
        $this->expectException(OutOfBoundsException::class);
        $specEndpoints->get('not-found');
    }

    public function testWithPut(): void
    {
        $method = new GetMethod();
        $routeEndpoint = new RouteEndpoint(
            $method,
            new TestController()
        );
        $specDir = dirForPath('/path/');
        $routeEndpointSpec = new RouteEndpointSpec(
            $specDir,
            $routeEndpoint
        );
        $specEndpoints = (new SpecEndpoints())->withPut($routeEndpointSpec);
        $this->assertCount(1, $specEndpoints);
        $this->assertTrue($specEndpoints->has($method->name()));
    }
}
