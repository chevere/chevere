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

use function Chevere\Components\Filesystem\filePhpReturnForPath;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Filesystem\FileInvalidContentsException;
use Chevere\Exceptions\Filesystem\FileNotExistsException;
use Chevere\Exceptions\Filesystem\FileReturnInvalidTypeException;
use Chevere\Exceptions\Filesystem\FilesystemException;
use Chevere\Exceptions\Filesystem\FileUnableToGetException;
use Chevere\Exceptions\Filesystem\FileWithoutContentsException;
use Chevere\Exceptions\Http\HttpMethodNotAllowedException;
use Chevere\Exceptions\Router\RouteNotRoutableException;
use Chevere\Exceptions\Router\RouteWithoutEndpointsException;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Http\MethodInterface;
use Chevere\Interfaces\Router\Route\RouteEndpointInterface;
use Chevere\Interfaces\Router\Route\RouteInterface;
use Chevere\Interfaces\Router\RouterInterface;
use Chevere\Interfaces\Router\RoutesInterface;

function routes(RouteInterface ...$namedRoutes): RoutesInterface
{
    return (new Routes())
        ->withAdded(...$namedRoutes);
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

/**
 *
 * @throws RouteNotRoutableException
 * @throws RouteWithoutEndpointsException
 * @throws InvalidArgumentException
 * @throws OverflowException
 *
 * @codeCoverageIgnore
 */
function router(string $group, RoutesInterface $routes): RouterInterface
{
    $router = new Router();
    foreach ($routes->getGenerator() as $route) {
        $router = $router->withAddedRoute($group, $route);
    }

    return $router;
}

/**
 * @throws FilesystemException
 * @throws FileNotExistsException
 * @throws FileUnableToGetException
 * @throws FileWithoutContentsException
 * @throws FileInvalidContentsException
 * @throws RuntimeException
 * @throws FileReturnInvalidTypeException
 *
 * @codeCoverageIgnore
 */
function importRoutes(string $path): RoutesInterface
{
    return filePhpReturnForPath($path)
        ->varType(new Type(RoutesInterface::class));
}
