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

namespace Chevere\Tests\Spec\Specs;

use function Chevere\Filesystem\dirForPath;
use Chevere\Http\Methods\GetMethod;
use Chevere\Router\Route\RouteEndpoint;
use Chevere\Spec\Specs\RouteEndpointSpec;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RouteEndpointSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $specDir = dirForPath('/spec/group/route-name/');
        $routeEndpoint = new RouteEndpoint(new GetMethod(), new TestController());
        $spec = new RouteEndpointSpec($specDir, $routeEndpoint);
        $specPathJson = $specDir->path()->__toString() .
            $routeEndpoint->method()->name() . '.json';
        $this->assertSame($specPathJson, $spec->jsonPath());
        $this->assertSame(
            [
                'name' => $routeEndpoint->method()->name(),
                'spec' => $specDir->path()->__toString() . $routeEndpoint->method()->name() . '.json',
                'description' => $routeEndpoint->method()->description(),
                'parameters' => $routeEndpoint->parameters(),
            ],
            $spec->toArray()
        );
    }
}
