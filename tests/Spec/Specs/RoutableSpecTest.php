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

use function Chevere\Components\Filesystem\dirForPath;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RouteLocator;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Components\Spec\Specs\RoutableSpec;
use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RoutableSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $repository = 'repo';
        $routeLocator = new RouteLocator($repository, '/route/path');
        $routePath = new RoutePath('/route/path');
        $specDir = dirForPath("/spec/${repository}/");
        $routeSpecPath = $specDir
            ->getChild(ltrim($routeLocator->path(), '/') . '/')
            ->path()
            ->toString() . 'route.json';
        $method = new GetMethod();
        $routeEndpoint = (new RouteEndpoint($method, new TestController()))
            ->withDescription('Test endpoint');
        $route = (new Route($routePath))
            ->withAddedEndpoint($routeEndpoint);
        $routable = new Routable($route);
        $spec = new RoutableSpec($specDir, $routable, $repository);
        $routeEndpoint = new RouteEndpointSpec(
            $specDir->getChild(ltrim($routeLocator->path(), '/') . '/'),
            $routeEndpoint
        );
        $this->assertSame($routeSpecPath, $spec->jsonPath());
        $this->assertSame(
            [
                'name' => $routePath->name(),
                'locator' => $routeLocator->toString(),
                'spec' => $routeSpecPath,
                'regex' => $routePath->regex()->toString(),
                'wildcards' => $routePath->wildcards()->toArray(),
                'endpoints' => [
                    $method->name() => $routeEndpoint->toArray(),
                ],
            ],
            $spec->toArray()
        );
    }
}
