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

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\ControllerResponse;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Controller\Interfaces\ControllerResponseInterface;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\RouteEndpoint;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class RouteEndpointTest extends TestCase
{
    public function testConstruct(): void
    {
        $method = new GetMethod;
        $controller = new RouteEndpointTestController;
        $routeEndpoint = new RouteEndpoint($method, $controller);
        $this->assertSame($method, $routeEndpoint->method());
        $this->assertSame($controller, $routeEndpoint->controller());
        $this->assertSame($method->description(), $routeEndpoint->description());
        /** @var string $name */
        foreach (array_keys($routeEndpoint->parameters()) as $name) {
            $this->assertTrue($controller->parameters()->hasParameterName($name));
        }
    }

    public function testWithDescription(): void
    {
        $description = 'Some description';
        $routeEndpoint = (new RouteEndpoint(new GetMethod, new RouteEndpointTestController))
            ->withDescription($description);
        $this->assertSame($description, $routeEndpoint->description());
    }

    public function testWithoutWrongParameter(): void
    {
        $controller = new RouteEndpointTestController;
        $this->expectException(OutOfBoundsException::class);
        (new RouteEndpoint(new GetMethod, $controller))
            ->withoutParameter('0x0');
    }

    public function testWithoutParameter(): void
    {
        $controller = new RouteEndpointTestController;
        $key = $controller->parameters()->map()->first()->toArray()['key'];
        $routeEndpoint = (new RouteEndpoint(new GetMethod, $controller))
            ->withoutParameter($key);
        $this->assertArrayNotHasKey($key, $routeEndpoint->parameters());
    }
}

final class RouteEndpointTestController extends Controller
{
    public function getParameters(): ControllerParametersInterface
    {
        return (new ControllerParameters)
            ->withParameter(new ControllerParameter('name', new Regex('/^[\w]+$/')))
            ->withParameter(new ControllerParameter('id', new Regex('/^[0-9]+$/')));
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponse(true);
    }
}
