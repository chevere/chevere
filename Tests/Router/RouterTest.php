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

namespace Chevere\Tests\Router;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Routables;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterIndex;
use Chevere\Components\Router\RouterRegex;
use Chevere\Tests\Router\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private Routable $routable;

    public function setUp(): void
    {
        $this->routable = new Routable(
            (new Route(new RouteName('test-name'), new RoutePath('/test')))
                ->withAddedEndpoint(
                    new RouteEndpoint(new GetMethod, new TestController)
                )
        );
    }

    public function testConstructor(): void
    {
        $router = new Router;
        $this->assertFalse($router->hasRegex());
        $this->assertCount(0, $router->routables()->map());
        $this->assertSame([], $router->index()->toArray());
    }

    public function testWithRegex(): void
    {
        $regex = new RouterRegex(new Regex('#^(?|/test (*:0))$#x'));
        $router = (new Router)->withRegex($regex);
        $this->assertTrue($router->hasRegex());
        $this->assertSame($regex, $router->regex());
    }

    public function testWithIndex(): void
    {
        $index = (new RouterIndex)->withAdded($this->routable, 'test-group');
        $router = (new Router)->withIndex($index);
        $this->assertSame($index, $router->index());
    }

    public function testWithRoutables(): void
    {
        $routables = (new Routables)->withPut($this->routable);
        $router = (new Router)->withRoutables($routables);
        $this->assertCount(1, $router->routables()->map());
        $this->assertTrue($router->routables()->hasKey(
            $this->routable->route()->name()->toString()
        ));
    }
}
