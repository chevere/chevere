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

namespace Chevere\Contracts\Route;

use Chevere\Contracts\Middleware\MiddlewareNamesContract;
use Chevere\Components\Route\Exceptions\RouteInvalidNameException;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Contracts\Http\MethodControllerContract;

interface RouteContract
{
    /** Regex pattern used to validate route name. */
    const REGEX_NAME = '/^[\w\-\.]+$/i';

    /**
     * Creates a new instance.
     */
    public function __construct(PathUriContract $pathUri);

    /**
     * Provides access to the PathUriContract instance.
     */
    public function pathUri(): PathUriContract;

    /**
     * Provides access to the maker array.
     */
    public function maker(): array;

    /**
     * Provides access to the route path representation, with placeholder wildcards like `/api/users/{0}`.
     */
    public function key(): string;

    /**
     * Returns a boolean indicating whether the instance is a dynamic route path.
     */
    public function isDynamic(): bool;

    /**
     * Provides access to the wildcards (if isDynamic).
     */
    public function wildcards(): array;

    /**
     * Return an instance with the specified name.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified name.
     *
     * @throws RouteInvalidNameException if $name doesn't match REGEX_NAME
     */
    public function withName(string $name): RouteContract;

    /**
     * Returns a boolean indicating whether the instance has a name.
     */
    public function hasName(): bool;

    /**
     * Provides access to the route name (if any).
     */
    public function name(): string;

    /**
     * Return an instance with the specified added WildcardContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added WildcardContract.
     *
     * @throws WildcardNotFoundException if the wildcard doesn't exists in the instance
     */
    public function withAddedWildcard(WildcardContract $wildcard): RouteContract;

    public function wheres(): array;

    public function middlewareNames(): MiddlewareNamesContract;

    public function wildcardName(int $key): string;

    public function regex(): string;

    /**
     * @param MethodControllerContract $methodController a HTTP method contract
     */
    public function withAddedMethodController(MethodControllerContract $methodController): RouteContract;

    public function withAddedMiddlewareName(string $middlewareName): RouteContract;

    /**
     * @param string $httpMethod an HTTP method
     */
    public function controller(string $httpMethod): string;

    /**
     * Fill object missing properties and whatnot.
     */
    public function withFiller(): RouteContract;

    /**
     * Gets route regex.
     *
     * @param string $pattern route path pattern (set)
     */
    public function getRegex(string $pattern): string;
}
