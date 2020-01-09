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

namespace Chevere\Components\Route\Contracts;

use Chevere\Components\Middleware\Contracts\MiddlewareNameCollectionContract;
use Chevere\Components\Route\Exceptions\RouteInvalidNameException;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Controller\Contracts\ControllerNameContract;
use Chevere\Components\Http\Contracts\MethodContract;
use Chevere\Components\Http\Contracts\MethodControllerNameCollectionContract;
use Chevere\Components\Middleware\Contracts\MiddlewareNameContract;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;

interface RouteContract
{
    public function __construct(PathUriContract $pathUri);

    /**
     * Provides access to the PathUriContract instance.
     */
    public function pathUri(): PathUriContract;

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
    public function withName(RouteNameContract $name): RouteContract;

    /**
     * Returns a boolean indicating whether the instance has a name.
     */
    public function hasName(): bool;

    /**
     * Provides access to the route name (if any).
     */
    public function name(): RouteNameContract;

    /**
     * Return an instance with the specified added WildcardContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added WildcardContract.
     *
     * @throws WildcardNotFoundException if the wildcard doesn't exists in the instance
     */
    public function withAddedWildcard(WildcardContract $wildcard): RouteContract;

    /**
     * Returns a boolean indicating whether the instance a WildcardCollectionContract.
     */
    public function hasWildcardCollection(): bool;

    /**
     * Provides access to the WildcardCollectionContract instance.
     */
    public function wildcardCollection(): WildcardCollectionContract;

    /**
     * Return an instance with the specified added MethodContract ControllerNameContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added MethodContract ControllerNameContract.
     */
    public function withAddedMethod(MethodContract $method, ControllerNameContract $controllerName): RouteContract;

    /**
     * Returns a boolean indicating whether the instance a MethodControllerNameCollectionContract.
     */
    public function hasMethodControllerNameCollection(): bool;

    /**
     * Provides access to the MethodControllerNameCollectionContract instance.
     */
    public function methodControllerNameCollection(): MethodControllerNameCollectionContract;

    /**
     * Get the controller name for the given MethodContract.
     *
     * @throws MethodNotFoundException if $method doesn't exists in the MethodControllerNameCollectionContract
     */
    public function controllerName(MethodContract $method): ControllerNameContract;

    /**
     * Return an instance with the specified added MiddlewareNameContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added MiddlewareNameContract.
     */
    public function withAddedMiddlewareName(MiddlewareNameContract $middlewareName): RouteContract;

    /**
     * Returns a boolean indicating whether the instance a MiddlewareNameCollectionContract.
     */
    public function hasMiddlewareNameCollection(): bool;

    /**
     * Provides access to the MiddlewareNameCollectionContract instance.
     */
    public function middlewareNameCollection(): MiddlewareNameCollectionContract;
}
