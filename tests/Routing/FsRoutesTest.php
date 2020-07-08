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

use Chevere\Components\Filesystem\FilesystemFactory;
use Chevere\Components\Route\RouteDecorator;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Routing\FsRoute;
use Chevere\Components\Routing\FsRoutes;
use Chevere\Exceptions\Routing\DecoratedRouteAlreadyAddedException;
use Chevere\Exceptions\Routing\RouteNameAlreadyAddedException;
use Chevere\Exceptions\Routing\RoutePathAlreadyAddedException;
use Chevere\Exceptions\Routing\RouteRegexAlreadyAddedException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Route\RouteDecoratorInterface;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;
use function Chevere\Components\Filesystem\getDirFromString;

final class FsRoutesTest extends TestCase
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
        $decoratedRoutes = new FsRoutes;
        $this->assertCount(0, $decoratedRoutes);
        $this->expectException(OutOfRangeException::class);
        $decoratedRoutes->get(0);
    }

    public function testWithDecorated(): void
    {
        $decoratedRoutes = new FsRoutes;
        $this->assertCount(0, $decoratedRoutes);
        $objects = [];
        $count = 0;
        foreach ([
            ['/path', 'name'],
            ['/path-alt', 'name-alt'],
        ] as $pos => $args) {
            $objects[$pos] = new FsRoute(
                $this->getDir(),
                new RoutePath($args[0]),
                $this->getRouteDecorator($args[1])
            );
            $decoratedRoutes = $decoratedRoutes
                ->withDecorated($objects[$pos]);
            $count++;
            $this->assertCount($count, $decoratedRoutes);
            $this->assertTrue($decoratedRoutes->contains($objects[$pos]));
            $this->assertSame($objects[$pos], $decoratedRoutes->get($count - 1));
        }
        $this->expectException(DecoratedRouteAlreadyAddedException::class);
        $decoratedRoutes->withDecorated($objects[$pos]);
    }

    public function testWithDecoratedDecoratorConflict(): void
    {
        $fs1 = new FsRoute(
            $this->getDir(),
            new RoutePath('/path'),
            $this->getRouteDecorator('name')
        );
        $fs2 = new FsRoute(
            $this->getDir(),
            new RoutePath('/path-alt'),
            $this->getRouteDecorator('name')
        );
        $decoratedRoutes = (new FsRoutes)->withDecorated($fs1);
        $this->expectException(RouteNameAlreadyAddedException::class);
        $decoratedRoutes->withDecorated($fs2);
    }

    public function testWithDecoratedRoutePathConflict(): void
    {
        $fs1 = new FsRoute(
            $this->getDir(),
            new RoutePath('/path'),
            $this->getRouteDecorator('name')
        );
        $fs2 = new FsRoute(
            $this->getDir(),
            new RoutePath('/path'),
            $this->getRouteDecorator('name-alt')
        );
        $decoratedRoutes = (new FsRoutes)->withDecorated($fs1);
        $this->expectException(RoutePathAlreadyAddedException::class);
        $decoratedRoutes->withDecorated($fs2);
    }

    public function testWithDecoratedRouteNameConflict(): void
    {
        $decoratedRoutes = (new FsRoutes)
            ->withDecorated(
                new FsRoute(
                    $this->getDir(),
                    new RoutePath('/path'),
                    $this->getRouteDecorator('name')
                )
            );
        $nameConflict = new FsRoute(
            $this->getDir(),
            new RoutePath('/path-alt'),
            $this->getRouteDecorator('name-dupe')
        );
        $this->expectException(RouteNameAlreadyAddedException::class);
        $decoratedRoutes->withDecorated($nameConflict);
    }

    public function testWithDecoratedRegexConflict(): void
    {
        $decoratedRoutes = (new FsRoutes)
            ->withDecorated(
                new FsRoute(
                    $this->getDir(),
                    new RoutePath('/path/{id}'),
                    $this->getRouteDecorator('name')
                )
            );
        $regexConflict = new FsRoute(
            $this->getDir(),
            new RoutePath('/path/{name}'),
            $this->getRouteDecorator('name-alt')
        );
        $this->expectException(RouteRegexAlreadyAddedException::class);
        $decoratedRoutes->withDecorated($regexConflict);
    }
}
