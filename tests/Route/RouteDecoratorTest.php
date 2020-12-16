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

namespace Chevere\Tests\Route;

use Chevere\Components\Route\RouteDecorator;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\Wildcards;
use PHPUnit\Framework\TestCase;

final class RouteDecoratorTest extends TestCase
{
    public function testConstructor(): void
    {
        $routeName = new RouteName('repo:/path/');
        $routeDecorator = new RouteDecorator($routeName);
        $this->assertSame($routeName, $routeDecorator->name());
        $this->assertCount(0, $routeDecorator->wildcards());
    }

    public function testWithWildcard(): void
    {
        $routeWildcards = new Wildcards();
        $routeDecorator = (new RouteDecorator(
            new RouteName('repo:/path/')
        ))
            ->withWildcards($routeWildcards);
        $this->assertSame($routeWildcards, $routeDecorator->wildcards());
    }
}
