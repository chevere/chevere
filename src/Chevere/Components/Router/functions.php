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

namespace Chevere\Components\Router;

use Chevere\Components\Message\Message;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Exceptions\Http\HttpMethodNotAllowedException;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Http\MethodInterface;
use Chevere\Interfaces\Router\Route\RouteEndpointInterface;
use Chevere\Interfaces\Router\Route\RouteInterface;
use Chevere\Interfaces\Router\RoutesInterface;

function routes(RouteInterface ...$namedRoutes): RoutesInterface
{
    return (new Routes())
        ->withPut(...$namedRoutes);
}

/**
 * @param ?string $name The route name, if not provided it will be same as the route path.
 * @param string $path The route path.
 * @param ControllerInterface ...$httpControllers Named arguments for httpMethod: ControllerName as `POST: PostController`.
 */
function route(?string $name = null, string $path, ControllerInterface ...$httpControllers): RouteInterface
{
    $route = new Route(
        $name ?? $path,
        new RoutePath($path)
    );
    foreach ($httpControllers as $httpMethod => $controller) {
        $method = RouteEndpointInterface::KNOWN_METHODS[$httpMethod] ?? null;
        if (is_null($method)) {
            throw new HttpMethodNotAllowedException(
                message: (new Message('Unknown HTTP method `%httpMethod%` provided for %controller% controller.'))
                    ->code('%httpMethod%', $httpMethod)
                    ->code('%controller%', $controller::class)
            );
        }
        /** @var MethodInterface $method */
        $method = new $method();
        $route = $route->withAddedEndpoint(
            new RouteEndpoint($method, $controller)
        );
    }

    return $route;
}
