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
use Chevere\Components\Spec\GroupSpec;
use Chevere\Components\Spec\RouteableSpec;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class GroupsSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $specPath = '/spec/';
        $groupName = 'group-name';
        $specGroupPath = $specPath . $groupName . '/';
        $specGroupPathJson = $specGroupPath . 'routes.json';
        $spec = new GroupSpec($specGroupPath);
        $this->assertSame($specGroupPathJson, $spec->jsonPath());
        $this->assertSame([
            'name' => $groupName,
            'spec' => $specGroupPathJson,
            'routes' => []
        ], $spec->toArray());
        $this->assertCount(0, $spec->objects());
    }

    public function testWithAddedRouteable(): void
    {
        $routeName = new RouteName('route-name');
        $specPath = '/spec/';
        $groupName = 'group-name';
        $groupSpecPath = $specPath . $groupName . '/';
        $routesSpecPathJson = $groupSpecPath . 'routes.json';
        $route = (new Route($routeName, new RoutePath('/route/path')))
            ->withAddedEndpoint(
                new RouteEndpoint(new GetMethod, new TestController)
            );
        $routeableSpec = new RouteableSpec(
            $groupSpecPath . $routeName->toString() . '/',
            new Routeable($route)
        );
        $spec = (new GroupSpec($groupSpecPath))
            ->withAddedRouteable($routeableSpec);
        $this->assertSame(
            [
                'name' => $groupName,
                'spec' => $routesSpecPathJson,
                'routes' => [$routeableSpec->toArray()],
            ],
            $spec->toArray()
        );
        $spec->objects()->rewind();
        $object = $spec->objects()->current();
        $this->assertNull($spec->objects()->getInfo());
        $this->assertSame($routeableSpec->jsonPath(), $object->jsonPath());
        $this->assertSame($routeableSpec->toArray(), $object->toArray());
    }
}
