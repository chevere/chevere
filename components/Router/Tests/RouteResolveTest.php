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

namespace Chevere\Components\Router\Tests;

use Chevere\Components\Route\RouteWildcards;
use Chevere\Components\Router\RouteResolve;
use PHPUnit\Framework\TestCase;

final class RouteResolveTest extends TestCase
{
    public function testConstruct(): void
    {
        $name = 'route-name';
        $routeWildcards = new RouteWildcards;
        $routeResolve = new RouteResolve($name, $routeWildcards);
        $this->assertSame($name, $routeResolve->name());
        $this->assertSame($routeWildcards, $routeResolve->routeWildcards());
    }
}
