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

namespace Chevere\Components\Spec\Tests\Specs;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Spec\SpecPath;
use Chevere\Components\Spec\Specs\GroupSpec;
use Chevere\Components\Spec\Specs\RouteableSpec;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class GroupSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $specPath = new SpecPath('/spec');
        $groupName = 'group-name';
        $specGroupPathJson = $specPath->getChild($groupName)
            ->getChild('routes.json')->pub();
        $spec = new GroupSpec($specPath, $groupName);
        $this->assertSame($specGroupPathJson, $spec->jsonPath());
        $this->assertSame(
            [
                'name' => $groupName,
                'spec' => $specGroupPathJson,
                'routes' => []
            ],
            $spec->toArray()
        );
    }

    public function testWithAddedRouteable(): void
    {
        $routeName = new RouteName('route-name');
        $specPath = new SpecPath('/spec');
        $groupName = 'group-name';
        $groupSpecPath = $specPath->getChild($groupName);
        $routesSpecPathJson = $groupSpecPath->getChild('routes.json')->pub();
        $route = (new Route($routeName, new RoutePath('/route/path')))
            ->withAddedEndpoint(
                new RouteEndpoint(new GetMethod, new TestController)
            );
        $routeableSpec = new RouteableSpec(
            $groupSpecPath->getChild($routeName->toString()),
            new Routable($route)
        );
        $spec = (new GroupSpec($specPath, $groupName))
            ->withAddedRouteableSpec($routeableSpec);
        $this->assertSame(
            [
                'name' => $groupName,
                'spec' => $routesSpecPathJson,
                'routes' => [$routeableSpec->key() => $routeableSpec->toArray()],
            ],
            $spec->toArray()
        );
    }
}
