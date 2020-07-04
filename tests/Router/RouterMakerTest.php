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

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\ControllerResponse;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\RouterMaker;
use Chevere\Exceptions\Router\RouteKeyConflictException;
use Chevere\Exceptions\Router\RouteNameConflictException;
use Chevere\Exceptions\Router\RoutePathExistsException;
use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use Chevere\Interfaces\Controller\ControllerResponseInterface;
use Chevere\Interfaces\Router\RoutableInterface;
use PHPUnit\Framework\TestCase;

final class RouterMakerTest extends TestCase
{
    public function testWithAddedRoutable(): void
    {
        $routable1 = $this->getRoutable('/path-1', 'route-name-1');
        $routable2 = $this->getRoutable('/path-2', 'route-name-2');
        $routerMaker = (new RouterMaker)
            ->withAddedRoutable($routable1, 'group')
            ->withAddedRoutable($routable2, 'group');
        $this->assertCount(2, $routerMaker->router()->routables());
        $routerIndex = $routerMaker->router()->index();
        $this->assertTrue($routerIndex->hasRouteName(
            $routable1->route()->name()->toString()
        ));
        $this->assertTrue($routerIndex->hasRouteName(
            $routable2->route()->name()->toString()
        ));
    }

    public function testWithAlreadyAddedPath(): void
    {
        $this->expectException(RoutePathExistsException::class);
        (new RouterMaker)
            ->withAddedRoutable(
                $this->getRoutable('/path', 'route-name'),
                'group'
            )
            ->withAddedRoutable(
                $this->getRoutable('/path', 'route-name2'),
                'another-group'
            );
    }

    public function testWithAlreadyAddedKey(): void
    {
        $routable1 = $this->getRoutable('/path/{name}', 'FooName');
        $routable2 = $this->getRoutable('/path/{id}', 'BarName');
        $this->expectException(RouteKeyConflictException::class);
        (new RouterMaker)
            ->withAddedRoutable($routable1, 'group')
            ->withAddedRoutable($routable2, 'another-group');
    }

    public function testWithAlreadyAddedName(): void
    {
        $routable1 = $this->getRoutable('/path1', 'SomeName');
        $routable2 = $this->getRoutable('/path2', 'SomeName');
        $routable3 = $this->getRoutable('/path3', 'SameName');
        $this->expectException(RouteNameConflictException::class);
        (new RouterMaker)
            ->withAddedRoutable($routable1, 'group1')
            ->withAddedRoutable($routable2, 'group2')
            ->withAddedRoutable($routable3, 'group3');
    }

    private function getRoutable(string $routePath, string $routeName): RoutableInterface
    {
        $route = new Route(new RouteName($routeName), new RoutePath($routePath));
        $route = $route
            ->withAddedEndpoint(
                new RouteEndpoint(new GetMethod, new RouterMakerTestController)
            );

        return new Routable($route);
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

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponse(true);
    }
}
