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

use Chevere\Components\Controller\Interfaces\ControllerNameInterface;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\Interfaces\MethodControllerNameInterface;
use Chevere\Components\Http\Interfaces\MethodControllerNamesInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameCollectionInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameInterface;
use Chevere\Components\Route\Exceptions\RouteNameInvalidException;

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
     * Return an instance with the specified added MethodControllerNameInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added MethodControllerNameInterface.
     *
     * Note: This method overrides any method already added.
     */
    public function withAddedMethodControllerName(MethodControllerNameInterface $methodControllerName): RouteInterface;

    /**
     * Provides access to the MethodControllerNamesInterface instance.
     */
    public function methodControllerNames(): MethodControllerNamesInterface;

    /**
     * Get the controller name for the given MethodInterface.
     *
     * @throws MethodNotFoundException if $method doesn't exists in the MethodControllerNamesInterface
     */
    public function controllerNameFor(MethodInterface $method): ControllerNameInterface;

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
