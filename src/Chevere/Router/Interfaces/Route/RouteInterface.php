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

namespace Chevere\Router\Interfaces\Route;

use Chevere\Router\Exceptions\Route\RouteEndpointConflictException;
use Chevere\Router\Exceptions\Route\RouteWildcardConflictException;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;

/**
 * Describes the component in charge of defining a route.
 */
interface RouteInterface
{
    public function __construct(string $name, RoutePathInterface $path);

    public function name(): string;

    /**
     * Provides access to the `$path` instance.
     */
    public function path(): RoutePathInterface;

    /**
     * Provides access to the file maker.
     */
    public function maker(): array;

    /**
     * Return an instance with the specified added `$routeEndpoint`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added `$routeEndpoint`.
     *
     * This method should allow to override any previous `$routeEndpoint`.
     *
     * @throws OverflowException
     * @throws RouteEndpointConflictException
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws RouteWildcardConflictException
     */
    public function withAddedEndpoint(RouteEndpointInterface $routeEndpoint): self;

    /**
     * Provides access to the endpoints instance.
     */
    public function endpoints(): RouteEndpointsInterface;
}
