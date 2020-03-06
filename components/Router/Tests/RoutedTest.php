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

use Chevere\Components\Route\RoutePath;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Router\Routed;
use PHPUnit\Framework\TestCase;

final class RoutedTest extends TestCase
{
    public function testConstruct(): void
    {
        $route = new Route(new RouteName('test'), new RoutePath('/path'));
        $wildcards = [];
        $routed = new Routed($route, $wildcards);
        $this->assertSame($route, $routed->route());
        $this->assertSame($wildcards, $routed->wildcards());
    }
}
