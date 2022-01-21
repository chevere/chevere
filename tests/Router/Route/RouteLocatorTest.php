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

use Chevere\Components\Router\Route\RouteLocator;
use PHPUnit\Framework\TestCase;

final class RouteLocatorTest extends TestCase
{
    public function testConstruct(): void
    {
        $repo = 'repo';
        $path = '/path';
        $routeLocator = new RouteLocator($repo, $path);
        $this->assertSame("${repo}:${path}", $routeLocator->__toString());
        $this->assertSame($repo, $routeLocator->repository());
        $this->assertSame($path, $routeLocator->path());
    }
}
