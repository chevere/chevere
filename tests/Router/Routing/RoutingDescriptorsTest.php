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

use Chevere\Components\Route\RouteDecorator;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routing\RoutingDescriptor;
use Chevere\Components\Router\Routing\RoutingDescriptors;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfRangeException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Route\RouteDecoratorInterface;
use PHPUnit\Framework\TestCase;
use function Chevere\Components\Filesystem\dirForPath;

final class RoutingDescriptorsTest extends TestCase
{
    private function getDir(): DirInterface
    {
        return dirForPath(__DIR__ . '/');
    }

    private function getRouteDecorator(string $path): RouteDecoratorInterface
    {
        return new RouteDecorator(
            new RouteName("repository:$path")
        );
    }

    public function testConstruct(): void
    {
        $descriptors = new RoutingDescriptors();
        $this->assertCount(0, $descriptors);
        $this->expectException(OutOfRangeException::class);
        $descriptors->get(0);
    }

    public function testWithAdded(): void
    {
        $descriptors = new RoutingDescriptors();
        $this->assertCount(0, $descriptors);
        $objects = [];
        $count = 0;
        $pos = '';
        foreach (['/path/', '/path-alt/'] as $pos => $path) {
            $objects[$pos] = new RoutingDescriptor(
                $this->getDir(),
                new RoutePath($path),
                $this->getRouteDecorator($path)
            );
            $descriptors = $descriptors
                ->withAdded($objects[$pos]);
            $count++;
            $this->assertCount($count, $descriptors);
            $this->assertTrue($descriptors->contains($objects[$pos]));
            $this->assertSame($objects[$pos], $descriptors->get($count - 1));
        }
        $this->expectException(OverflowException::class);
        $descriptors->withAdded($objects[$pos]);
    }

    public function testWithDecoratedDecoratorConflict(): void
    {
        $fs1 = new RoutingDescriptor(
            $this->getDir(),
            new RoutePath('/path/'),
            $this->getRouteDecorator('/path/')
        );
        $fs2 = new RoutingDescriptor(
            $this->getDir(),
            new RoutePath('/path-alt/'),
            $this->getRouteDecorator('/path/')
        );
        $descriptors = (new RoutingDescriptors())->withAdded($fs1);
        $this->expectException(InvalidArgumentException::class);
        $descriptors->withAdded($fs2);
    }

    public function testWithDecoratedRoutePathConflict(): void
    {
        $fs1 = new RoutingDescriptor(
            $this->getDir(),
            new RoutePath('/path/'),
            $this->getRouteDecorator('/path/')
        );
        $fs2 = new RoutingDescriptor(
            $this->getDir(),
            new RoutePath('/path/'),
            $this->getRouteDecorator('/path/')
        );
        $descriptors = (new RoutingDescriptors())->withAdded($fs1);
        $this->expectException(InvalidArgumentException::class);
        $descriptors->withAdded($fs2);
    }

    public function testWithDecoratedRouteNameConflict(): void
    {
        $descriptors = (new RoutingDescriptors())
            ->withAdded(
                new RoutingDescriptor(
                    $this->getDir(),
                    new RoutePath('/path/'),
                    $this->getRouteDecorator('/path/')
                )
            );
        $nameConflict = new RoutingDescriptor(
            $this->getDir(),
            new RoutePath('/path-alt/'),
            $this->getRouteDecorator('/path/')
        );
        $this->expectException(InvalidArgumentException::class);
        $descriptors->withAdded($nameConflict);
    }
}
