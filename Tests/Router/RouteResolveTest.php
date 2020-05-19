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

namespace Chevere\Tests\Router;

use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RouteWildcards;
use Chevere\Components\Router\RouteResolve;
use PHPUnit\Framework\TestCase;

final class RouteResolveTest extends TestCase
{
    public function testConstruct(): void
    {
        $name = new RouteName('route-name');
        $wildcards = new RouteWildcards;
        $resolve = new RouteResolve($name, $wildcards);
        $this->assertSame($name, $resolve->name());
        $this->assertSame($wildcards, $resolve->wildcards());
    }
}
