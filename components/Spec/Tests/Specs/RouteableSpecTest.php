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

use Chevere\Components\Http\MethodController;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\Route;
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
        $specPath = '/spec/group/' . $routeName->toString() . '/route.json';
        $route = new Route(
            $routeName,
            new RoutePath('/route/path')
        );
        $route = $route->withAddedMethodController(
            new MethodController(
                new GetMethod(),
                new TestController
            )
        );
        $spec = new RouteableSpec(new Routeable($route), $specPath);
        // xdd($spec);
        // $this->assertSame(
        //     [
        //     ],
        //     $spec->toArray()
        // );
    }
}
