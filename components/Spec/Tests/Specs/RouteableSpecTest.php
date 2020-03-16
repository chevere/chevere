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

namespace Chevere\Components\Spec\Specs\Tests;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Spec\RouteableSpec;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RouteableSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $routeName = new RouteName('route-name');
        $routePath = new RoutePath('/route/path');
        $specPath = '/spec/group/' . $routeName->toString() . '/';
        $routeSpecPath = $specPath . 'route.json';
        $route = new Route($routeName, $routePath);
        $method = new GetMethod;
        $routeEndpoint = (new RouteEndpoint($method, new TestController))
            ->withDescription('Test endpoint')
            ->withParameters(['name' => 'Test name']);
        $route = $route->withAddedEndpoint($routeEndpoint);
        $routeable = new Routeable($route);
        $spec = new RouteableSpec($specPath, $routeable);
        $endpointSpecPath = $specPath . $method->name() . '.json';
        $expectedEndpoint = [
            'method' => $method->name(),
            'spec' => $endpointSpecPath,
            'description' => $routeEndpoint->description(),
            'parameters' => $routeEndpoint->parameters()
        ];
        $this->assertSame($routeSpecPath, $spec->jsonPath());
        $this->assertSame(
            [
                'name' => $routeName->toString(),
                'spec' => $routeSpecPath,
                'path' => $routePath->toString(),
                'wildcards' => [],
                'endpoints' => [$expectedEndpoint]
            ],
            $spec->toArray()
        );
        $spec->objects()->rewind();
        $object = $spec->objects()->current();
        $this->assertSame($endpointSpecPath, $object->jsonPath());
        $this->assertSame($expectedEndpoint, $object->toArray());
    }
}
