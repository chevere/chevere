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

namespace Chevere\Components\Spec\Specs\Tests;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Spec\RouteEndpointSpec;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RouteEndpointSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $specPath = '/spec/group/route-name/';
        $routeEndpoint = new RouteEndpoint(new GetMethod, new TestController);
        $spec = new RouteEndpointSpec($specPath, $routeEndpoint);
        $specPathJson = $specPath . $routeEndpoint->method()->name() . '.json';
        $this->assertSame($specPathJson, $spec->jsonPath());
        $this->assertSame(
            [
                'method' => $routeEndpoint->method()->name(),
                'spec' => $specPath . $routeEndpoint->method()->name() . '.json',
                'description' => $routeEndpoint->method()->description(),
                'parameters' => [],
            ],
            $spec->toArray()
        );
    }
}
