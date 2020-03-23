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

namespace Chevere\Components\Route\Interfaces;

use Chevere\Components\Route\RouteEndpointsMap;

interface RouteEndpointsInterface
{
    /**
     * Return an instance with the specified added MethodControllerInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified MethodControllerInterface.
     *
     * Note: This method overrides any method already added.
     */
    public function withAddedRouteEndpoint(RouteEndpointInterface $routeEndpoint): RouteEndpointsInterface;

    public function routeEndpointsMap(): RouteEndpointsMap;
}
