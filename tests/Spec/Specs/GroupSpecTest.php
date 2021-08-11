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
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RouteLocator;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Components\Spec\Specs\GroupSpec;
use Chevere\Components\Spec\Specs\RouteSpec;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class GroupSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $specDir = dirForPath('/spec/');
        $repository = 'repo';
        $specGroupPathJson = $specDir
            ->getChild("${repository}/")
            ->path()
            ->toString() . 'routes.json';
        $spec = new GroupSpec($specDir, $repository);
        $this->assertSame($specGroupPathJson, $spec->jsonPath());
        $this->assertSame(
            [
                'name' => $repository,
                'spec' => $specGroupPathJson,
                'routes' => [],
            ],
            $spec->toArray()
        );
    }

    public function testWithAddedRoutable(): void
    {
        $repository = 'repo';
        $routeLocator = new RouteLocator($repository, '/path');
        $specDir = dirForPath('/spec/');
        $repository = 'repo';
        $groupSpecDir = $specDir->getChild("${repository}/");
        $routesSpecPathJson = $groupSpecDir->path()->toString() . 'routes.json';
        $route = (new Route('test', new RoutePath('/route/path')))
            ->withAddedEndpoint(
                new RouteEndpoint(new GetMethod(), new TestController())
            );
        $routableSpec = new RouteSpec(
            $groupSpecDir->getChild(ltrim($routeLocator->path(), '/') . '/'),
            $route,
            $repository
        );
        $spec = (new GroupSpec($specDir, $repository))
            ->withAddedRoutableSpec($routableSpec);
        $this->assertSame(
            [
                'name' => $repository,
                'spec' => $routesSpecPathJson,
                'routes' => [
                    $routableSpec->key() => $routableSpec->toArray(),
                ],
            ],
            $spec->toArray()
        );
    }
}
