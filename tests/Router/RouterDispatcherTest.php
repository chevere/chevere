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

use Chevere\Components\Router\RouteParsers\StrictStd;
use Chevere\Components\Router\RouterDispatcher;
use Chevere\Exceptions\Http\HttpMethodNotAllowedException;
use Chevere\Exceptions\Router\RouteNotFoundException;
use Chevere\Tests\Router\_resources\src\TestController;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use PHPUnit\Framework\TestCase;

final class RouterDispatcherTest extends TestCase
{
    private function getRouteCollector(): RouteCollector
    {
        return new RouteCollector(new StrictStd(), new GroupCountBased());
    }

    public function testNotFound(): void
    {
        $routeDispatcher = new RouterDispatcher($this->getRouteCollector());
        $this->expectException(RouteNotFoundException::class);
        $routeDispatcher->dispatch('get', '/');
    }

    public function testFound(): void
    {
        $routeCollector = $this->getRouteCollector();
        $routeCollector->addRoute('GET', '/', TestController::class);
        $routeDispatcher = new RouterDispatcher($routeCollector);
        $this->expectNotToPerformAssertions();
        $routeDispatcher->dispatch('GET', '/');
    }

    public function testHttpMethodNotAllowed(): void
    {
        $routeCollector = $this->getRouteCollector();
        $routeCollector->addRoute('GET', '/', function () {
        });
        $routeDispatcher = new RouterDispatcher($routeCollector);
        $this->expectException(HttpMethodNotAllowedException::class);
        $routeDispatcher->dispatch('Asdf', '/');
    }
}
