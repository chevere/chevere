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
use Chevere\Components\Routing\RouteEndpointIterator;
use PHPUnit\Framework\TestCase;

final class RouteEndpointIteratorTest extends TestCase
{
    public function testObjects(): void
    {
        $dir = new Dir(new Path(__DIR__ . '/_resources/routes/articles/{id}/'));
        $endpointIterator = new RouteEndpointIterator($dir);
        $objects = $endpointIterator->routeEndpointObjects();
        $this->assertCount(1, $objects);
        $objects->rewind();
        while ($objects->valid()) {
            $this->assertInstanceOf(
                RouteEndpointInterface::class,
                $objects->current()
            );
            $objects->next();
        }
    }

    public function testWrongObjects(): void
    {
        $dir = new Dir(new Path(__DIR__ . '/_resources/wrong_routes/articles/'));
        $this->expectException(ExpectingRouteDecoratorException::class);
        new RouteEndpointIterator($dir);
    }
}
