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
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Routables;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RoutablesTest extends TestCase
{
    public function testWithPut(): void
    {
        $route = new Route(
            new RouteName('some-name'),
            new RoutePath('/some-path')
        );
        $key = $route->name()->toString();
        $route = $route->withAddedEndpoint(
            new RouteEndpoint(
                new GetMethod,
                new TestController
            )
        );
        $routable = new Routable($route);
        $routables = (new Routables)->withPut($routable);
        $this->assertTrue($routables->hasKey($key));
        $this->assertSame($routable, $routables->get($key));
    }
}
