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

namespace Chevere\Components\Route;

use Chevere\Components\Middleware\Interfaces\MiddlewareNameCollectionInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameInterface;
use Chevere\Components\Middleware\MiddlewareNameCollection;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointsInterface;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;

final class Route implements RouteInterface
{
    /** @var string The named identifier */
    private RouteNameInterface $name;

    private RoutePathInterface $routePath;

    /** @var array An array containg details about the instance maker */
    private array $maker;

    private MiddlewareNameCollectionInterface $middlewareNameCollection;

    private RouteEndpointsInterface $endpoints;

    public function __construct(RouteNameInterface $name, RoutePathInterface $routePath)
    {
        $this->name = $name;
        $this->routePath = $routePath;
        $this->maker = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[0];
        $this->endpoints = new RouteEndpoints;
        $this->middlewareNameCollection = new MiddlewareNameCollection;
    }

    public function name(): RouteNameInterface
    {
        return $this->name;
    }

    public function path(): RoutePathInterface
    {
        return $this->routePath;
    }

    public function maker(): array
    {
        return $this->maker;
    }

    public function withAddedEndpoint(RouteEndpointInterface $routeEndpoint): RouteInterface
    {
        $new = clone $this;
        $new->endpoints = $new->endpoints
            ->withAddedRouteEndpoint(
                $routeEndpoint
            );

        return $new;
    }

    public function endpoints(): RouteEndpointsInterface
    {
        return $this->endpoints;
    }

    public function withAddedMiddlewareName(MiddlewareNameInterface $middlewareName): RouteInterface
    {
        $new = clone $this;
        $new->middlewareNameCollection = $new->middlewareNameCollection
            ->withAddedMiddlewareName($middlewareName);

        return $new;
    }

    public function middlewareNameCollection(): MiddlewareNameCollectionInterface
    {
        return $this->middlewareNameCollection;
    }
}
