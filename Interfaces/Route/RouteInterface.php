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

namespace Chevere\Interfaces\Route;

use Chevere\Exceptions\Route\RouteNameInvalidException;
use Chevere\Interfaces\Middleware\MiddlewareNameCollectionInterface;
use Chevere\Interfaces\Middleware\MiddlewareNameInterface;

interface RouteInterface
{
    /**
     * @throws RouteNameInvalidException if $name doesn't match REGEX_NAME
     */
    public function __construct(RouteNameInterface $name, RoutePathInterface $routePath);

    /**
     * Provides access to the route name (if any).
     */
    public function name(): RouteNameInterface;

    /**
     * Provides access to the RoutePathInterface instance.
     */
    public function path(): RoutePathInterface;

    /**
     * Provides access to the file maker array.
     */
    public function maker(): array;

    /**
     * Return an instance with the specified added RouteEndpointInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added RouteEndpointInterface.
     *
     * Note: This method overrides any method already added.
     */
    public function withAddedEndpoint(RouteEndpointInterface $routeEndpoint): RouteInterface;

    /**
     * Provides access to the RouteEndpointsInterface instance.
     */
    public function endpoints(): RouteEndpointsInterface;

    /**
     * Return an instance with the specified added MiddlewareNameInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added MiddlewareNameInterface.
     */
    public function withAddedMiddlewareName(MiddlewareNameInterface $middlewareName): RouteInterface;

    /**
     * Provides access to the MiddlewareNameCollectionInterface instance.
     */
    public function middlewareNameCollection(): MiddlewareNameCollectionInterface;
}
