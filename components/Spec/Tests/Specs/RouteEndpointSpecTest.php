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

use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Spec\RouteEndpointSpec;
use PHPUnit\Framework\TestCase;

final class RouteEndpointSpecTest extends TestCase
{
    private RouteEndpoint $routeEndpoint;

    public function testConstruct(): void
    {
        $specPath = '/spec/group/route-name/GET.json';
        $this->routeEndpoint = include dirname(__DIR__) . '/_resources/endpoints/Get.php';
        $spec = new RouteEndpointSpec($this->routeEndpoint, $specPath);
        $this->assertSame(
            [
                'method' => $this->routeEndpoint->method()->name(),
                'spec' => $specPath,
                'description' => $this->routeEndpoint->method()->description(),
                'parameters' => [],
            ],
            $spec->toArray()
        );
    }
}
