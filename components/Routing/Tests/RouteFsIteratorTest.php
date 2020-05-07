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
use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Routing\Exceptions\ExpectingRouteNameException;
use Chevere\Components\Routing\FsRoutesMaker;
use PHPUnit\Framework\TestCase;

final class RouteFsIteratorTest extends TestCase
{
    public function testObjects(): void
    {
        $dir = new DirFromString(__DIR__ . '/_resources/routes/');
        $fsRoutesMaker = new FsRoutesMaker($dir);
        $fsRoutes = $fsRoutesMaker->fsRoutes();
        $this->assertCount(2, $fsRoutes);
        for ($i = 0; $i < $fsRoutes->count(); ++$i) {
            $fsRoute = $fsRoutes->get($i);
            $this->assertInstanceOf(
                RoutePathInterface::class,
                $fsRoute->routePath()
            );
            $this->assertInstanceOf(
                RouteDecoratorInterface::class,
                $fsRoute->routeDecorator()
            );
        }
    }

    public function testWrongObjects(): void
    {
        $dir = new DirFromString(__DIR__ . '/_resources/wrong-routes/');
        $this->expectException(ExpectingRouteNameException::class);
        new FsRoutesMaker($dir);
    }
}
