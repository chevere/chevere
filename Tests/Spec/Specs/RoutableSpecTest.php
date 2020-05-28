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

namespace Chevere\Tests\Spec\Specs;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Spec\SpecPath;
use Chevere\Components\Spec\Specs\RoutableSpec;
use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RoutableSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $routeName = new RouteName('route-name');
        $routePath = new RoutePath('/route/path');
        $specPath = new SpecPath('/spec/group');
        $routeSpecPath = $specPath->getChild($routeName->toString() . '/route.json')->pub();
        $method = new GetMethod;
        $routeEndpoint = (new RouteEndpoint($method, new TestController))
            ->withDescription('Test endpoint');
        $route = (new Route($routeName, $routePath))
            ->withAddedEndpoint($routeEndpoint);
        $routable = new Routable($route);
        $spec = new RoutableSpec($specPath, $routable);
        $routeEndpoint = new RouteEndpointSpec(
            $specPath->getChild($routeName->toString()),
            $routeEndpoint
        );
        $this->assertSame($routeSpecPath, $spec->jsonPath());
        $this->assertSame(
            [
                'name' => $routeName->toString(),
                'spec' => $routeSpecPath,
                'path' => $routePath->toString(),
                'regex' => $routePath->regex()->toNoDelimiters(),
                'wildcards' => $routePath->wildcards()->map()->toArray(),
                'endpoints' => [
                    $method->name() => $routeEndpoint->toArray()
                ]
            ],
            $spec->toArray()
        );
    }
}
