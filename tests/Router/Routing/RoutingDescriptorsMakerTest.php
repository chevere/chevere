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

namespace Chevere\Tests\Router\Routing;

use function Chevere\Components\Filesystem\dirForPath;
use Chevere\Components\Router\Routing\RoutingDescriptorsMaker;
use Chevere\Exceptions\Router\Routing\ExpectingControllerException;
use Chevere\Interfaces\Router\Route\RouteDecoratorInterface;
use Chevere\Interfaces\Router\Route\RoutePathInterface;
use PHPUnit\Framework\TestCase;

final class RoutingDescriptorsMakerTest extends TestCase
{
    public function testObjects(): void
    {
        $dir = dirForPath(__DIR__ . '/_resources/routes/');
        $fsRoutesMaker = new RoutingDescriptorsMaker('routes', $dir);
        $fsRoutes = $fsRoutesMaker->descriptors();
        $this->assertCount(3, $fsRoutes);
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
        $dir = dirForPath(__DIR__ . '/_resources/wrong-routes/');
        $this->expectException(ExpectingControllerException::class);
        new RoutingDescriptorsMaker('wrong-routes', $dir);
    }
}
