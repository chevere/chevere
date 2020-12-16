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

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Spec\SpecDir;
use Chevere\Components\Spec\SpecEndpoints;
use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Tests\Router\Route\_resources\src\TestController;
use PHPUnit\Framework\TestCase;
use function Chevere\Components\Filesystem\dirForPath;

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
        $specPath = new SpecDir(dirForPath('/path/'));
        $routeEndpointSpec = new RouteEndpointSpec(
            $specPath,
            $routeEndpoint
        );
        $specEndpoints = (new SpecEndpoints())->withPut($routeEndpointSpec);
        $this->assertCount(1, $specEndpoints);
        $this->assertTrue($specEndpoints->has($method->name()));
    }
}
