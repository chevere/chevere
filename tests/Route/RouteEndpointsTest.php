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

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteEndpoints;
use Chevere\Tests\Route\_resources\src\GetArticleController;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class RouteEndpointsTest extends TestCase
{
    public function testConstruct(): void
    {
        $method = new GetMethod;
        $routeEndpoints = new RouteEndpoints;
        $this->assertCount(0, $routeEndpoints);
        $this->assertFalse($routeEndpoints->hasKey($method->name()));
        $this->expectException(OutOfBoundsException::class);
        $routeEndpoints->get($method->name());
    }

    public function testWithPut(): void
    {
        $method = new GetMethod;
        $routeEndpoint = new RouteEndpoint($method, new GetArticleController);
        $routeEndpoints = (new RouteEndpoints)->withPut($routeEndpoint);
        $this->assertTrue($routeEndpoints->hasKey($method->name()));
        $this->assertSame($routeEndpoints->get($method->name()), $routeEndpoint);
    }
}
