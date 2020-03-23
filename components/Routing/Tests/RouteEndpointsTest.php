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

namespace Chevere\Components\Routing\Tests;

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Routing\Exceptions\ExpectingRouteDecoratorException;
use Chevere\Components\Routing\RouteEndpointsMaker;
use PHPUnit\Framework\TestCase;

final class RouteEndpointsTest extends TestCase
{
    public function testObjects(): void
    {
        $dir = new Dir(new Path(__DIR__ . '/_resources/routes/articles/{id}/'));
        $endpointIterator = new RouteEndpointsMaker($dir);
        $routeEndpoints = $endpointIterator->routeEndpointsMap();
        $this->assertCount(1, $routeEndpoints->map());
        /** @var RouteEndpoint $routeEndpoint */
        foreach ($routeEndpoints->map() as $routeEndpoint) {
            $this->assertInstanceOf(
                RouteEndpointInterface::class,
                $routeEndpoint
            );
        }
    }

    public function testWrongObjects(): void
    {
        $dir = new Dir(new Path(__DIR__ . '/_resources/wrong-routes/articles/'));
        $this->expectException(ExpectingRouteDecoratorException::class);
        new RouteEndpointsMaker($dir);
    }
}
