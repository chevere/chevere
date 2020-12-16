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

namespace Chevere\Components\Router\Routing;

use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RouteEndpoints;
use Chevere\Components\Router\Router;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OutOfRangeException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Filesystem\FileReturnInvalidTypeException;
use Chevere\Exceptions\Filesystem\PathDotSlashException;
use Chevere\Exceptions\Filesystem\PathDoubleDotsDashException;
use Chevere\Exceptions\Filesystem\PathExtraSlashesException;
use Chevere\Exceptions\Filesystem\PathNotAbsoluteException;
use Chevere\Exceptions\Router\Route\RouteEndpointConflictException;
use Chevere\Exceptions\Router\Route\RouteWildcardConflictException;
use Chevere\Exceptions\Router\Routing\ExpectingControllerException;
use Chevere\Interfaces\Action\ControllerInterface;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Router\Route\RouteEndpointInterface;
use Chevere\Interfaces\Router\Route\RouteEndpointsInterface;
use Chevere\Interfaces\Router\RouterInterface;
use Chevere\Interfaces\Router\Routing\RoutingDescriptorsInterface;
use function Chevere\Components\Filesystem\filePhpReturnForPath;

/**
 * @codeCoverageIgnore
 *
 * @throws OutOfRangeException
 * @throws OverflowException
 * @throws RouteEndpointConflictException
 * @throws InvalidArgumentException
 * @throws OutOfBoundsException
 * @throws RouteWildcardConflictException
 */
function routerForRoutingDescriptors(RoutingDescriptorsInterface $descriptors, string $group): RouterInterface
{
    $router = new Router();
    foreach ($descriptors->getGenerator() as $descriptor) {
        $routePath = $descriptor->path();
        $routeDecorator = $descriptor->decorator();
        // foreach ($routeDecorator->wildcards()->getGenerator() as $routeWildcard) {
        //     $routePath = $routePath->withWildcard($routeWildcard); // @codeCoverageIgnore
        // }
        $routeEndpoints = routeEndpointsForDir($descriptor->dir());
        $route = new Route($routePath);
        foreach ($routeEndpoints->keys() as $key) {
            $route = $route->withAddedEndpoint(
                $routeEndpoints->get($key)
            );
        }
        $router = $router
            ->withAddedRoutable(new Routable($route), $group);
    }

    return $router;
}

/**
 * @codeCoverageIgnore
 *
 * @throws PathDotSlashException
 * @throws PathDoubleDotsDashException
 * @throws PathExtraSlashesException
 * @throws PathNotAbsoluteException
 * @throws ExpectingControllerException
 */
function routeEndpointsForDir(DirInterface $dir): RouteEndpointsInterface
{
    $routeEndpoints = new RouteEndpoints();
    $path = $dir->path();
    foreach (RouteEndpointInterface::KNOWN_METHODS as $name => $methodClass) {
        $controllerPath = $path->getChild($name . '.php');
        if (!$controllerPath->exists()) {
            continue;
        }

        try {
            $controller = filePhpReturnForPath($controllerPath->absolute())
                ->withStrict(false)
                ->varType(new Type(ControllerInterface::class));
        } catch (FileReturnInvalidTypeException $e) {
            throw new ExpectingControllerException($e->message());
        }
        $routeEndpoints = $routeEndpoints
            ->withPut(
                new RouteEndpoint(new $methodClass(), $controller)
            );
    }

    return $routeEndpoints;
}
