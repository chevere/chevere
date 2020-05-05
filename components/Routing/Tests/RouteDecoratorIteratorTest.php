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
use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Routing\Exceptions\ExpectingRouteDecoratorException;
use Chevere\Components\Routing\Interfaces\RoutePathIteratorInterface;
use Chevere\Components\Routing\RouteDecoratorIterator;
use PHPUnit\Framework\TestCase;

final class RouteDecoratorIteratorTest extends TestCase
{
    public function testObjects(): void
    {
        $dir = new Dir(new Path(__DIR__ . '/_resources/routes/'));
        $iterator = new RouteDecoratorIterator($dir);
        $decoratedRoutes = $iterator->decoratedRoutes();
        $this->assertCount(2, $decoratedRoutes);
        for ($i = 0; $i < $decoratedRoutes->count(); ++$i) {
            $decoratedRoute = $decoratedRoutes->get($i);
            $this->assertInstanceOf(
                RoutePathInterface::class,
                $decoratedRoute->routePath()
            );
            $this->assertInstanceOf(
                RouteDecoratorInterface::class,
                $decoratedRoute->routeDecorator()
            );
            $this->assertStringEndsWith(
                RoutePathIteratorInterface::ROUTE_DECORATOR_BASENAME,
                $decoratedRoute->routeDecorator()->whereIs()
            );
        }
    }

    public function testWrongObjects(): void
    {
        $dir = new Dir(new Path(__DIR__ . '/_resources/wrong-routes/'));
        $this->expectException(ExpectingRouteDecoratorException::class);
        new RouteDecoratorIterator($dir);
    }
}
