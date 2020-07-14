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

namespace Chevere\Tests\Routing;

use Chevere\Components\Routing\RoutingDescriptorsMaker;
use Chevere\Exceptions\Routing\ExpectingRouteNameException;
use Chevere\Interfaces\Route\RouteDecoratorInterface;
use Chevere\Interfaces\Route\RoutePathInterface;
use PHPUnit\Framework\TestCase;
use function Chevere\Components\Filesystem\getDirFromString;

final class RoutingDescriptorsMakerTest extends TestCase
{
    public function testObjects(): void
    {
        $dir = getDirFromString(__DIR__ . '/_resources/routes/');
        $fsRoutesMaker = new RoutingDescriptorsMaker($dir);
        $fsRoutes = $fsRoutesMaker->descriptors();
        $this->assertCount(2, $fsRoutes);
        for ($i = 0; $i < $fsRoutes->count(); ++$i) {
            $fsRoute = $fsRoutes->get($i);
            $this->assertInstanceOf(
                RoutePathInterface::class,
                $fsRoute->path()
            );
            $this->assertInstanceOf(
                RouteDecoratorInterface::class,
                $fsRoute->decorator()
            );
        }
    }

    public function testWrongObjects(): void
    {
        $dir = getDirFromString(__DIR__ . '/_resources/wrong-routes/');
        $this->expectException(ExpectingRouteNameException::class);
        new RoutingDescriptorsMaker($dir);
    }
}
