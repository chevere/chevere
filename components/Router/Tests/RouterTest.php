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

namespace Chevere\Components\Router\Tests;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\Routeables;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterIndex;
use Chevere\Components\Router\RouterRegex;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private CacheHelper $cacheHelper;

    private Routeable $routeable;

    public function setUp(): void
    {
        $this->cacheHelper = new CacheHelper(__DIR__);
        $this->routeable = new Routeable(
            (new Route(new RouteName('test-name'), new RoutePath('/test')))
                ->withAddedEndpoint(
                    new RouteEndpoint(new GetMethod, new TestController)
                )
        );
    }

    public function tearDown(): void
    {
        $this->cacheHelper->tearDown();
    }

    public function testConstructor(): void
    {
        $router = new Router();
        $this->assertFalse($router->hasRegex());
        $this->assertCount(0, $router->routeables()->map());
        $this->assertSame([], $router->index()->toArray());
    }

    public function testWithRegex(): void
    {
        $regex = new RouterRegex(new Regex('#^(?|/test (*:0))$#x'));
        $router = (new Router())->withRegex($regex);
        $this->assertTrue($router->hasRegex());
        $this->assertSame($regex, $router->regex());
    }

    public function testWithIndex(): void
    {
        $index = (new RouterIndex)->withAdded($this->routeable, 'test-group');
        $router = (new Router)->withIndex($index);
        $this->assertSame($index, $router->index());
    }

    public function testWithRouteables(): void
    {
        $routeables = new Routeables;
        $routeables->put($this->routeable);
        $router = (new Router)->withRouteables($routeables);
        $this->assertCount(1, $router->routeables()->map());
        $this->assertTrue($router->routeables()->hasKey(
            $this->routeable->route()->name()->toString()
        ));
    }
}
