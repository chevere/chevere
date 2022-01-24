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

namespace Chevere\Tests\Router\Route;

use Chevere\Router\Route\RouteDecorator;
use Chevere\Router\Route\RouteLocator;
use Chevere\Router\Route\RouteWildcards;
use PHPUnit\Framework\TestCase;

final class RouteDecoratorTest extends TestCase
{
    public function testConstructor(): void
    {
        $routeLocator = new RouteLocator('repo', '/path');
        $routeDecorator = new RouteDecorator($routeLocator);
        $this->assertSame($routeLocator, $routeDecorator->locator());
        $this->assertCount(0, $routeDecorator->wildcards());
    }

    public function testWithWildcard(): void
    {
        $routeWildcards = new RouteWildcards();
        $routeDecorator = new RouteDecorator(new RouteLocator('repo', '/path'));
        $routeDecoratorWithWildcards = $routeDecorator
            ->withWildcards($routeWildcards);
        $this->assertNotSame($routeDecorator, $routeDecoratorWithWildcards);
        $this->assertEquals(
            $routeWildcards,
            $routeDecoratorWithWildcards->wildcards()
        );
    }
}
