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
use Chevere\Components\Router\RouteableObjectsRead;
use Chevere\Components\Spec\GroupSpec;
use Chevere\Components\Spec\RouteableSpec;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;
use SplObjectStorage;

final class GroupsSpecTest extends TestCase
{
    // public function testConstruct(): void
    // {
    //     $routeName = new RouteName('route-name');
    //     $routePath = new RoutePath('/route/path');
    //     $specPath = '/spec/group/';
    //     $routesSpecPath = $specPath . 'routes.json';
    //     $route = new Route($routeName, $routePath);
    //     $routeEndpoint = (new RouteEndpoint(new GetMethod, new TestController));
    //     $route = $route->withAddedEndpoint($routeEndpoint);
    //     $routeable = new Routeable($route);
    //     $objectStorage = new SplObjectStorage;
    //     $objectStorage->attach($routeable);
    //     $objects = new RouteableObjectsRead($objectStorage);
    //     $spec = new GroupSpec($specPath, $objects);
    //     // xdd($spec->toArray());
    //     // $this->assertSame($routesSpecPath, $spec->jsonPath());
    //     $this->assertSame(
    //         [
    //             'name' => $routeName->toString(),
    //             'spec' => $routesSpecPath,
    //             'routes' => $routePath->toString(),
    //         ],
    //         $spec->toArray()
    //     );
    //     $spec->objects()->rewind();
    //     $object = $spec->objects()->current();
    //     $this->assertSame($endpointSpecPath, $object->jsonPath());
    //     $this->assertSame($expectedEndpoint, $object->toArray());
    // }
}
