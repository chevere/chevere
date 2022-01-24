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

use function Chevere\Filesystem\dirForPath;
use Chevere\Http\Methods\GetMethod;
use Chevere\Router\Route\Route;
use Chevere\Router\Route\RouteEndpoint;
use Chevere\Router\Route\RouteLocator;
use Chevere\Router\Route\RoutePath;
use Chevere\Spec\Specs\RouteSpec;
use Chevere\Spec\Specs\RouteEndpointSpec;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RouteSpecTest extends TestCase
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
            ->__toString() . 'route.json';
        $method = new GetMethod();
        $routeEndpoint = (new RouteEndpoint($method, new TestController()))
            ->withDescription('Test endpoint');
        $route = (new Route('test', $routePath))
            ->withAddedEndpoint($routeEndpoint);
        $spec = new RouteSpec($specDir, $route, $repository);
        $routeEndpoint = new RouteEndpointSpec(
            $specDir->getChild(ltrim($routeLocator->path(), '/') . '/'),
            $routeEndpoint
        );
        $this->assertSame($routeSpecPath, $spec->jsonPath());
        $this->assertSame(
            [
                'name' => $routePath->name(),
                'locator' => $routeLocator->__toString(),
                'spec' => $routeSpecPath,
                'regex' => $routePath->regex()->__toString(),
                'wildcards' => $routePath->wildcards()->toArray(),
                'endpoints' => [
                    $method->name() => $routeEndpoint->toArray(),
                ],
            ],
            $spec->toArray()
        );
    }
}
