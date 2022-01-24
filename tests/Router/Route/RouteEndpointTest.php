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

use Chevere\Http\Methods\GetMethod;
use Chevere\Router\Route\RouteEndpoint;
use Chevere\Tests\Router\Route\_resources\src\RouteEndpointTestController;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class RouteEndpointTest extends TestCase
{
    public function testConstruct(): void
    {
        $method = new GetMethod();
        $controller = new RouteEndpointTestController();
        $routeEndpoint = new RouteEndpoint($method, $controller);
        $this->assertSame($method, $routeEndpoint->method());
        $this->assertSame($controller, $routeEndpoint->controller());
        $this->assertSame($method->description(), $routeEndpoint->description());
        /** @var string $name */
        foreach (array_keys($routeEndpoint->parameters()) as $name) {
            $this->assertTrue($controller->parameters()->has($name));
        }
    }

    public function testWithDescription(): void
    {
        $description = 'Some description';
        $routeEndpoint = (new RouteEndpoint(new GetMethod(), new RouteEndpointTestController()))
            ->withDescription($description);
        $this->assertSame($description, $routeEndpoint->description());
    }

    public function testWithoutWrongParameter(): void
    {
        $controller = new RouteEndpointTestController();
        $this->expectException(OutOfBoundsException::class);
        (new RouteEndpoint(new GetMethod(), $controller))
            ->withoutParameter('0x0');
    }

    public function testWithoutParameter(): void
    {
        $controller = new RouteEndpointTestController();
        $iterator = $controller->parameters()->getIterator();
        $iterator->rewind();
        $key = $iterator->key() ?? 'name';
        $routeEndpoint = (new RouteEndpoint(new GetMethod(), $controller))
            ->withoutParameter($key);
        $this->assertArrayNotHasKey($key, $routeEndpoint->parameters());
    }
}
