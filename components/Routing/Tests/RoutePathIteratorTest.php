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
use Chevere\Components\Routing\Interfaces\RoutePathIteratorInterface;
use Chevere\Components\Routing\RoutePathIterator;
use PHPUnit\Framework\TestCase;

final class RoutePathIteratorTest extends TestCase
{
    public function testRecursiveIterator(): void
    {
        $rootDir = new Dir(new Path(__DIR__ . '/_resources/routes/'));
        $routePathIterator = new RoutePathIterator($rootDir);
        $recursiveIterator = $routePathIterator->recursiveIterator();
        $this->assertCount(2, $recursiveIterator);
        $recursiveIterator->rewind();
        while ($recursiveIterator->valid()) {
            $this->assertStringEndsWith(
                RoutePathIteratorInterface::ROUTE_DECORATOR_BASENAME,
                $recursiveIterator->current()->getPathName()
            );
            $recursiveIterator->next();
        }
    }

    public function testObjects(): void
    {
        $rootDir = new Dir(new Path(__DIR__ . '/_resources/routes/'));
        $routePathIterator = new RoutePathIterator($rootDir);
        $objectStorage = $routePathIterator->objects();
        $this->assertCount(2, $objectStorage);
        $objectStorage->rewind();
        while ($objectStorage->valid()) {
            $this->assertInstanceOf(
                RoutePathInterface::class,
                $objectStorage->current()
            );
            $this->assertInstanceOf(
                RouteDecoratorInterface::class,
                $objectStorage->routeDecorator()
            );
            $objectStorage->next();
        }
    }
}
