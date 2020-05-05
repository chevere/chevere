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

use Chevere\Components\ExceptionHandler\Exceptions\Exception;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionInterface;
use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteDecorator;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Routing\DecoratedRoute;
use Chevere\Components\Routing\DecoratedRoutes;
use Chevere\Components\Routing\Exceptions\DecoratedRouteAlreadyAddedException;
use Chevere\Components\Routing\Exceptions\RouteDecoratorFileAlreadyAddedException;
use Chevere\Components\Routing\Exceptions\RouteNameAlreadyAddedException;
use Chevere\Components\Routing\Exceptions\RoutePathAlreadyAddedException;
use Chevere\Components\Routing\Exceptions\RouteRegexAlreadyAddedException;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;

final class DecoratedRoutesTest extends TestCase
{
    public function getRouteDecorator(string $name): RouteDecoratorInterface
    {
        return include __DIR__ . '/_resources/DecoratedRoutesTest/' . $name . '.php';
    }

    public function testConstruct(): void
    {
        $decoratedRoutes = new DecoratedRoutes;
        $this->assertCount(0, $decoratedRoutes);
        $this->expectException(OutOfRangeException::class);
        $decoratedRoutes->get(0);
    }

    public function testWithDecorated(): void
    {
        $decoratedRoutes = new DecoratedRoutes;
        $this->assertCount(0, $decoratedRoutes);
        $objects = [];
        $count = 0;
        foreach ([
            ['/path', 'name'],
            ['/path-alt', 'name-alt'],
        ] as $pos => $args) {
            $objects[$pos] = new DecoratedRoute(
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
        $decorated1 = new DecoratedRoute(
            new RoutePath('/path'),
            $this->getRouteDecorator('name')
        );
        $decorated2 = new DecoratedRoute(
            new RoutePath('/path-alt'),
            $this->getRouteDecorator('name')
        );
        $decoratedRoutes = (new DecoratedRoutes)->withDecorated($decorated1);
        $this->expectException(RouteDecoratorFileAlreadyAddedException::class);
        $decoratedRoutes->withDecorated($decorated2);
    }

    public function testWithDecoratedRoutePathConflict(): void
    {
        $decorated1 = new DecoratedRoute(
            new RoutePath('/path'),
            $this->getRouteDecorator('name')
        );
        $decorated2 = new DecoratedRoute(
            new RoutePath('/path'),
            $this->getRouteDecorator('name-alt')
        );
        $decoratedRoutes = (new DecoratedRoutes)->withDecorated($decorated1);
        $this->expectException(RoutePathAlreadyAddedException::class);
        $decoratedRoutes->withDecorated($decorated2);
    }

    public function testWithDecoratedRouteNameConflict(): void
    {
        $decoratedRoutes = (new DecoratedRoutes)
            ->withDecorated(
                new DecoratedRoute(
                    new RoutePath('/path'),
                    $this->getRouteDecorator('name')
                )
            );
        $nameConflict = new DecoratedRoute(
            new RoutePath('/path-alt'),
            $this->getRouteDecorator('name-dupe')
        );
        $this->expectException(RouteNameAlreadyAddedException::class);
        $decoratedRoutes->withDecorated($nameConflict);
    }

    public function testWithDecoratedRegexConflict(): void
    {
        $decoratedRoutes = (new DecoratedRoutes)
            ->withDecorated(
                new DecoratedRoute(
                    new RoutePath('/path/{id}'),
                    $this->getRouteDecorator('name')
                )
            );
        $regexConflict = new DecoratedRoute(
            new RoutePath('/path/{name}'),
            $this->getRouteDecorator('name-alt')
        );
        $this->expectException(RouteRegexAlreadyAddedException::class);
        $decoratedRoutes->withDecorated($regexConflict);
    }
}
