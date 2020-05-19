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

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Spec\SpecPath;
use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RouteEndpointSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $specPath = new SpecPath('/spec/group/route-name');
        $routeEndpoint = new RouteEndpoint(new GetMethod, new TestController);
        $spec = new RouteEndpointSpec($specPath, $routeEndpoint);
        $specPathJson = $specPath->getChild($routeEndpoint->method()->name() . '.json')->pub();
        $this->assertSame($specPathJson, $spec->jsonPath());
        $this->assertSame(
            [
                'method' => $routeEndpoint->method()->name(),
                'spec' => $specPath->getChild($routeEndpoint->method()->name() . '.json')->pub(),
                'description' => $routeEndpoint->method()->description(),
                'parameters' => $routeEndpoint->parameters(),
            ],
            $spec->toArray()
        );
    }
}
