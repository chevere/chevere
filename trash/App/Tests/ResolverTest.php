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

namespace Chevere\Components\App\Tests;

use Chevere\Components\App\App;
use Chevere\Components\App\Build;
use Chevere\Components\App\Builder;
use Chevere\Components\App\Exceptions\ResolverException;
use Chevere\Components\App\Interfaces\ResolvableInterface;
use Chevere\Components\App\Resolvable;
use Chevere\Components\App\Resolver;
use Chevere\Components\App\Services;
use Chevere\Components\Cache\Cache;
use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Filesystem\Path;
use Chevere\Interfaces\Http\RequestInterface;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\Request;
use Chevere\Components\Http\Response;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterCache;
use Chevere\Components\Router\RouterMaker;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class ResolverTest extends TestCase
{
    private function getResolvable(RequestInterface $request): ResolvableInterface
    {
        $route = (new Route(new RoutePath('/resolver')))
            ->withAddedMethod(
                new Method('GET'),
                new ControllerName(TestController::class)
            );
        $routerCache = new RouterCache(new Cache(new DirFromString(__DIR__)));
        $routerMaker = (new RouterMaker($routerCache))
            ->withAddedRoutable(
                new Routable($route),
                'test'
            );
        $router = $routerMaker->router();
        $services = (new Services())
            ->withRouter($router);
        $app = new App($services, new Response());
        $app = $app->withRequest($request);

        return
            new Resolvable(
                new Builder(
                    new Build($app)
                )
            );
    }

    public function testRouteNotFound(): void
    {
        $resolvable = $this->getResolvable(
            new Request(
                new Method('GET'),
                new RoutePath('/not-found')
            )
        );
        $this->expectExceptionCode(404);
        $this->expectException(ResolverException::class);
        new Resolver($resolvable);
    }

    public function testMethodNotFound(): void
    {
        $resolvable = $this->getResolvable(
            new Request(
                new Method('POST'),
                new RoutePath('/resolver')
            )
        );
        $this->expectExceptionCode(405);
        $this->expectException(ResolverException::class);
        new Resolver($resolvable);
    }
}
