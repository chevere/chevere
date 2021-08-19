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

namespace Chevere\Interfaces\Router;

use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Router\Route\RouteInterface;
use FastRoute\RouteCollector;

/**
 * Describes the component in charge of handling routing.
 */
interface RouterInterface
{
    /**
     * Return an instance with the specified added `$route`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added `$route`.
     *
     * @throws InvalidArgumentException if `$group` is invalid.
     * @throws OverflowException if `$route` has been already added.
     */
    public function withAddedRoute(string $group, RouteInterface $route): self;

    /**
     * Provides access to the index instance.
     */
    public function index(): RouterIndexInterface;

    /**
     * Provides access to the routes instance.
     */
    public function routes(): RoutesInterface;

    /**
     * Provides access to the route collector instance.
     */
    public function routeCollector(): RouteCollector;
}
