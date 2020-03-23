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

namespace Chevere\Components\Route\Tests;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RouteEndpointTest extends TestCase
{
    public function testConstruct(): void
    {
        $method = new GetMethod;
        $controller = new TestController;
        $routeEndpoint = new RouteEndpoint($method, $controller);
        $this->assertSame($method, $routeEndpoint->method());
        $this->assertSame($controller, $routeEndpoint->controller());
        $this->assertSame($method->description(), $routeEndpoint->description());
        $this->assertSame([], $routeEndpoint->parameters());
    }

    public function testWithDescription(): void
    {
        $description = 'Some description';
        $routeEndpoint = (new RouteEndpoint(new GetMethod, new TestController))
            ->withDescription($description);
        $this->assertSame($description, $routeEndpoint->description());
    }

    public function testWithParameters(): void
    {
        $parameters = ['some' => 'parameter'];
        $routeEndpoint = (new RouteEndpoint(new GetMethod, new TestController))
            ->withParameters($parameters);
        $this->assertSame($parameters, $routeEndpoint->parameters());
    }
}
