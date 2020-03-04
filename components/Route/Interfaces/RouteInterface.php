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

use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\ControllerNameInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameCollectionInterface;
use Chevere\Components\Route\Exceptions\RouteInvalidNameException;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Http\Interfaces\MethodControllerNameCollectionInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameInterface;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;

interface RouteInterface
{
    /**
     * @throws RouteInvalidNameException if $name doesn't match REGEX_NAME
     */
    public function __construct(RouteNameInterface $name, PathUriInterface $pathUri);

    /**
     * Provides access to the PathUriInterface instance.
     */
    public function pathUri(): PathUriInterface;

    /**
     * Provides access to the file maker array.
     */
    public function maker(): array;

    /**
     * Provides access to the route name (if any).
     */
    public function name(): RouteNameInterface;

    /**
     * Return an instance with the specified added MethodInterface & ControllerInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added MethodInterface & ControllerInterface.
     *
     * Note: This method overrides any method already added.
     */
    public function withAddedMethodController(MethodInterface $method, ControllerInterface $controller): RouteInterface;

    /**
     * Provides access to the MethodControllerNameCollectionInterface instance.
     */
    public function methodControllerNameCollection(): MethodControllerNameCollectionInterface;

    /**
     * Get the controller name for the given MethodInterface.
     *
     * @throws MethodNotFoundException if $method doesn't exists in the MethodControllerNameCollectionInterface
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
     * Returns a boolean indicating whether the instance a MiddlewareNameCollectionInterface.
     */
    public function hasMiddlewareNameCollection(): bool;

    /**
     * Provides access to the MiddlewareNameCollectionInterface instance.
     */
    public function middlewareNameCollection(): MiddlewareNameCollectionInterface;
}
