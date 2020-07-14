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

use Chevere\Components\Route\RouteDecorator;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Routing\RoutingDescriptor;
use Chevere\Components\Routing\RoutingDescriptors;
use Chevere\Exceptions\Routing\DecoratedRouteAlreadyAddedException;
use Chevere\Exceptions\Routing\RouteNameAlreadyAddedException;
use Chevere\Exceptions\Routing\RoutePathAlreadyAddedException;
use Chevere\Exceptions\Routing\RouteRegexAlreadyAddedException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Route\RouteDecoratorInterface;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;
use function Chevere\Components\Filesystem\getDirFromString;

final class RoutingDescriptorsTest extends TestCase
{
    private function getDir(): DirInterface
    {
        return getDirFromString(__DIR__ . '/');
    }

    private function getRouteDecorator(string $name): RouteDecoratorInterface
    {
        return new RouteDecorator(
            include __DIR__ . '/_resources/FsRoutesTest/' . $name . '.php'
        );
    }

    public function testConstruct(): void
    {
        $descriptors = new RoutingDescriptors;
        $this->assertCount(0, $descriptors);
        $this->expectException(OutOfRangeException::class);
        $descriptors->get(0);
    }

    public function testWithDecorated(): void
    {
        $descriptors = new RoutingDescriptors;
        $this->assertCount(0, $descriptors);
        $objects = [];
        $count = 0;
        foreach ([
            ['/path', 'name'],
            ['/path-alt', 'name-alt'],
        ] as $pos => $args) {
            $objects[$pos] = new RoutingDescriptor(
                $this->getDir(),
                new RoutePath($args[0]),
                $this->getRouteDecorator($args[1])
            );
            $descriptors = $descriptors
                ->withAdded($objects[$pos]);
            $count++;
            $this->assertCount($count, $descriptors);
            $this->assertTrue($descriptors->contains($objects[$pos]));
            $this->assertSame($objects[$pos], $descriptors->get($count - 1));
        }
        $this->expectException(DecoratedRouteAlreadyAddedException::class);
        $descriptors->withAdded($objects[$pos]);
    }

    public function testWithDecoratedDecoratorConflict(): void
    {
        $fs1 = new RoutingDescriptor(
            $this->getDir(),
            new RoutePath('/path'),
            $this->getRouteDecorator('name')
        );
        $fs2 = new RoutingDescriptor(
            $this->getDir(),
            new RoutePath('/path-alt'),
            $this->getRouteDecorator('name')
        );
        $descriptors = (new RoutingDescriptors)->withAdded($fs1);
        $this->expectException(RouteNameAlreadyAddedException::class);
        $descriptors->withAdded($fs2);
    }

    public function testWithDecoratedRoutePathConflict(): void
    {
        $fs1 = new RoutingDescriptor(
            $this->getDir(),
            new RoutePath('/path'),
            $this->getRouteDecorator('name')
        );
        $fs2 = new RoutingDescriptor(
            $this->getDir(),
            new RoutePath('/path'),
            $this->getRouteDecorator('name-alt')
        );
        $descriptors = (new RoutingDescriptors)->withAdded($fs1);
        $this->expectException(RoutePathAlreadyAddedException::class);
        $descriptors->withAdded($fs2);
    }

    public function testWithDecoratedRouteNameConflict(): void
    {
        $descriptors = (new RoutingDescriptors)
            ->withAdded(
                new RoutingDescriptor(
                    $this->getDir(),
                    new RoutePath('/path'),
                    $this->getRouteDecorator('name')
                )
            );
        $nameConflict = new RoutingDescriptor(
            $this->getDir(),
            new RoutePath('/path-alt'),
            $this->getRouteDecorator('name-dupe')
        );
        $this->expectException(RouteNameAlreadyAddedException::class);
        $descriptors->withAdded($nameConflict);
    }

    public function testWithDecoratedRegexConflict(): void
    {
        $descriptors = (new RoutingDescriptors)
            ->withAdded(
                new RoutingDescriptor(
                    $this->getDir(),
                    new RoutePath('/path/{id}'),
                    $this->getRouteDecorator('name')
                )
            );
        $regexConflict = new RoutingDescriptor(
            $this->getDir(),
            new RoutePath('/path/{name}'),
            $this->getRouteDecorator('name-alt')
        );
        $this->expectException(RouteRegexAlreadyAddedException::class);
        $descriptors->withAdded($regexConflict);
    }
}
