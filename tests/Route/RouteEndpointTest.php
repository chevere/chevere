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
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
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
            $this->assertTrue($controller->parameters()->has($name));
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
        $generator = $controller->parameters()->getGenerator();
        $generator->rewind();
        $key = $generator->key() ?? 'name';
        $routeEndpoint = (new RouteEndpoint(new GetMethod, $controller))
            ->withoutParameter($key);
        $this->assertArrayNotHasKey($key, $routeEndpoint->parameters());
    }
}

final class RouteEndpointTestController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAdded(
                (new Parameter('name'))
                    ->withRegex(new Regex('/^[\w]+$/'))
            )
            ->withAdded(
                (new Parameter('id'))
                    ->withRegex(new Regex('/^[0-9]+$/'))
            );
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}
