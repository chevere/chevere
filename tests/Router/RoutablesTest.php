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
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Routables;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RoutablesTest extends TestCase
{
    public function testWithPut(): void
    {
        $route = new Route(
            new RoutePath('/some-path')
        );
        $key = $route->path()->toString();
        $route = $route->withAddedEndpoint(
            new RouteEndpoint(
                new GetMethod(),
                new TestController()
            )
        );
        $routable = new Routable($route);
        $routables = (new Routables())->withPut($routable);
        $this->assertFalse($routables->has('test', 'some'));
        $this->assertTrue($routables->has($key));
        $this->assertSame($routable, $routables->get($key));
        $this->expectException(OutOfBoundsException::class);
        $routables->get('not-found');
    }
}
