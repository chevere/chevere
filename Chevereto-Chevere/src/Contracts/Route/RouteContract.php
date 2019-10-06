<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Contracts\Route;

use Chevere\Contracts\Http\MethodContract;
use Chevere\Contracts\Http\MethodsContract;

interface RouteContract
{
    /**
     * Route constructor.
     *
     * @param string $uri        Route uri (key string)
     * @param string $controller Callable for GET
     */
    public function __construct(string $uri, string $controller = null);

    public function id(): string;

    public function path(): string;

    public function name(): string;

    public function hasName(): bool;

    public function wheres(): array;

    public function middlewares(): array;

    public function wildcardName(int $key): string;

    public function keyPowerSet(): array;

    public function type(): string;

    public function regex(): string;

    /**
     * @param string $name route name, must be unique
     */
    public function withName(string $name): RouteContract;

    /**
     * Sets where conditionals for the route wildcards.
     *
     * @param string $wildcardName wildcard name
     * @param string $regex        regex pattern
     */
    public function withWhere(string $wildcardName, string $regex): RouteContract;

    /**
     * Sets HTTP method to callable binding. Allocates Routes.
     *
     * @param MethodContract $method a HTTP method contract
     */
    public function withAddedMethod(MethodContract $method): RouteContract;

    /**
     * Sets HTTP method to callable binding (multiple version).
     *
     * @param MethodsContract $methods a HTTP methods contract
     */
    public function withMethods(MethodsContract $methods): RouteContract;

    public function withId(string $id): RouteContract;

    public function withAddedMiddleware(string $callable): RouteContract;

    /**
     * @param string $httpMethod an HTTP method
     */
    public function getController(string $httpMethod): string;

    /**
     * Fill object missing properties and whatnot.
     */
    public function fill(): RouteContract;

    /**
     * Gets route regex.
     *
     * @param string $pattern route path pattern (set)
     */
    public function getRegex(string $pattern): string;
}
