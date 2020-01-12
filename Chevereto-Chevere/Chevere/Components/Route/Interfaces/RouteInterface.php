<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Route\Interfaces;

use Chevere\Components\Middleware\Interfaces\MiddlewareNameCollectionInterface;
use Chevere\Components\Route\Exceptions\RouteInvalidNameException;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Controller\Interfaces\ControllerNameInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Http\Interfaces\MethodControllerNameCollectionInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameInterface;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;

interface RouteInterface
{
    public function __construct(PathUriInterface $pathUri);

    /**
     * Provides access to the PathUriContract instance.
     */
    public function pathUri(): PathUriInterface;

    /**
     * Provides access to the file maker array.
     */
    public function maker(): array;

    /**
     * Provides access to the regex string.
     */
    public function regex(): string;

    /**
     * Return an instance with the specified name.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified name.
     *
     * @throws RouteInvalidNameException if $name doesn't match REGEX_NAME
     */
    public function withName(RouteNameInterface $name): RouteInterface;

    /**
     * Returns a boolean indicating whether the instance has a name.
     */
    public function hasName(): bool;

    /**
     * Provides access to the route name (if any).
     */
    public function name(): RouteNameInterface;

    /**
     * Return an instance with the specified added WildcardContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added WildcardContract.
     *
     * @throws WildcardNotFoundException if the wildcard doesn't exists in the instance
     */
    public function withAddedWildcard(WildcardInterface $wildcard): RouteInterface;

    /**
     * Returns a boolean indicating whether the instance a WildcardCollectionContract.
     */
    public function hasWildcardCollection(): bool;

    /**
     * Provides access to the WildcardCollectionContract instance.
     */
    public function wildcardCollection(): WildcardCollectionInterface;

    /**
     * Return an instance with the specified added MethodInterface ControllerNameContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added MethodInterface ControllerNameContract.
     */
    public function withAddedMethod(MethodInterface $method, ControllerNameInterface $controllerName): RouteInterface;

    /**
     * Returns a boolean indicating whether the instance a MethodControllerNameCollectionContract.
     */
    public function hasMethodControllerNameCollection(): bool;

    /**
     * Provides access to the MethodControllerNameCollectionContract instance.
     */
    public function methodControllerNameCollection(): MethodControllerNameCollectionInterface;

    /**
     * Get the controller name for the given MethodInterface.
     *
     * @throws MethodNotFoundException if $method doesn't exists in the MethodControllerNameCollectionContract
     */
    public function controllerName(MethodInterface $method): ControllerNameInterface;

    /**
     * Return an instance with the specified added MiddlewareNameContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added MiddlewareNameContract.
     */
    public function withAddedMiddlewareName(MiddlewareNameInterface $middlewareName): RouteInterface;

    /**
     * Returns a boolean indicating whether the instance a MiddlewareNameCollectionContract.
     */
    public function hasMiddlewareNameCollection(): bool;

    /**
     * Provides access to the MiddlewareNameCollectionContract instance.
     */
    public function middlewareNameCollection(): MiddlewareNameCollectionInterface;
}
