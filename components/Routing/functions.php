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

namespace Chevere\Components\Routing;

use Chevere\Components\Route\Route;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Router;
use Chevere\Interfaces\Router\RouterInterface;
use Chevere\Interfaces\Routing\RoutingDescriptorsInterface;

function getRouterFromRoutingDescriptors(RoutingDescriptorsInterface $descriptors): RouterInterface
{
    $router = new Router;
    for ($i = 0; $i < $descriptors->count(); ++$i) {
        $descriptor = $descriptors->get($i);
        $routePath = $descriptor->path();
        $routeDecorator = $descriptor->decorator();
        foreach ($routeDecorator->wildcards()->getGenerator() as $routeWildcard) {
            $routePath = $routePath->withWildcard($routeWildcard); // @codeCoverageIgnore
        }
        $routeEndpointsMaker = new RouteEndpointsIterator($descriptor->dir());
        $routeEndpoints = $routeEndpointsMaker->routeEndpoints();
        $route = new Route($routeDecorator->name(), $routePath);
        /** @var string $key */
        foreach ($routeEndpoints->keys() as $key) {
            $route = $route->withAddedEndpoint(
                $routeEndpoints->get($key)
            );
        }
        $router = $router
            ->withAddedRoutable(new Routable($route), 'routing');
    }

    return $router;
}
