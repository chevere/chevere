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

namespace Chevere\Components\Spec\Tests;

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterGroups;
use Chevere\Components\Router\RouterIndex;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Router\RouterNamed;
use Chevere\Components\Spec\Exceptions\SpecInvalidArgumentException;
use Chevere\Components\Spec\SpecMaker;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class SpecMakerTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(SpecInvalidArgumentException::class);
        new SpecMaker(
            new RoutePath('/spec'),
            new Dir(new Path(__DIR__ . '/')),
            new Router
        );
    }

    // public function testConstruct(): void
    // {
    //     $route = new Route(new RouteName('route-name'), new RoutePath('/route-path'));
    //     $route = $route->withAddedEndpoint(
    //         new RouteEndpoint(new PutMethod, new TestController)
    //     );
    //     $routerMaker = (new RouterMaker)
    //         ->withAddedRouteable(
    //             new Routeable($route),
    //             'group-name'
    //         );
    //     $specMaker = new SpecMaker(
    //         new RoutePath('/spec'),
    //         new Dir(new Path(__DIR__ . '/_resources/spec/')),
    //         $routerMaker->router()
    //     );
    //     xdd($specMaker);
    // }
}
