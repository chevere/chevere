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

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Exceptions\RouteKeyConflictException;
use Chevere\Components\Router\Exceptions\RouteNameConflictException;
use Chevere\Components\Router\Exceptions\RoutePathExistsException;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\RouterMaker;
use PHPUnit\Framework\TestCase;

final class RouterMakerTest extends TestCase
{
    public function testWithAddedRouteable(): void
    {
        $routeable1 = $this->getRouteable('/path-1', 'route-name-1');
        $routeable2 = $this->getRouteable('/path-2', 'route-name-2');
        $routerMaker = (new RouterMaker)
            ->withAddedRouteable($routeable1, 'group')
            ->withAddedRouteable($routeable2, 'group');
        $this->assertCount(2, $routerMaker->router()->routeables()->map());
        $routerIndex = $routerMaker->router()->index();
        $this->assertTrue($routerIndex->hasRouteName(
            $routeable1->route()->name()->toString()
        ));
        $this->assertTrue($routerIndex->hasRouteName(
            $routeable2->route()->name()->toString()
        ));
    }

    public function testWithAlreadyAddedPath(): void
    {
        $this->expectException(RoutePathExistsException::class);
        (new RouterMaker)
            ->withAddedRouteable(
                $this->getRouteable('/path', 'route-name'),
                'group'
            )
            ->withAddedRouteable(
                $this->getRouteable('/path', 'route-name2'),
                'another-group'
            );
    }

    public function testWithAlreadyAddedKey(): void
    {
        $routeable1 = $this->getRouteable('/path/{name}', 'FooName');
        $routeable2 = $this->getRouteable('/path/{id}', 'BarName');
        $this->expectException(RouteKeyConflictException::class);
        (new RouterMaker)
            ->withAddedRouteable($routeable1, 'group')
            ->withAddedRouteable($routeable2, 'another-group');
    }

    public function testWithAlreadyAddedName(): void
    {
        $routeable1 = $this->getRouteable('/path1', 'SomeName');
        $routeable2 = $this->getRouteable('/path2', 'SomeName');
        $routeable3 = $this->getRouteable('/path3', 'SameName');
        $this->expectException(RouteNameConflictException::class);
        (new RouterMaker)
            ->withAddedRouteable($routeable1, 'group1')
            ->withAddedRouteable($routeable2, 'group2')
            ->withAddedRouteable($routeable3, 'group3');
    }

    private function getRouteable(string $routePath, string $routeName): RouteableInterface
    {
        $route = new Route(new RouteName($routeName), new RoutePath($routePath));
        $route = $route
            ->withAddedEndpoint(
                new RouteEndpoint(new GetMethod, new RouterMakerTestController)
            );

        return new Routeable($route);
    }
}

final class RouterMakerTestController extends Controller
{
    public function getParameters(): ControllerParametersInterface
    {
        return (new ControllerParameters)
            ->withParameter(new ControllerParameter('name', new Regex('/^[\w]+$/')))
            ->withParameter(new ControllerParameter('id', new Regex('/^[0-9]+$/')));
    }

    public function run(ControllerArgumentsInterface $arguments): void
    {
        // does nothing
    }
}
