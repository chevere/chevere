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

use Chevere\Contracts\HttpFoundation\MethodContract;
use Chevere\Contracts\HttpFoundation\MethodsContract;

interface RouteContract
{
    /**
     * Route constructor.
     *
     * @param string $uri        Route uri (key string)
     * @param string $controller Callable for GET
     */
    public function __construct(string $uri, string $controller = null);

    /**
     * @param string $name route name, must be unique
     */
    public function setName(string $name): RouteContract;

    /**
     * Sets where conditionals for the route wildcards.
     *
     * @param string $wildcardName wildcard name
     * @param string $regex        regex pattern
     */
    public function setWhere(string $wildcardName, string $regex): RouteContract;

    /**
     * Sets where conditionals for the route wildcards (multiple version).
     *
     * @param array $wildcardsPatterns An array containing [wildcardName => regexPattern,]
     */
    public function setWheres(array $wildcardsPatterns): RouteContract;

    /**
     * Sets HTTP method to callable binding. Allocates Routes.
     *
     * @param MethodContract $method a HTTP method contract
     */
    public function setMethod(MethodContract $method): RouteContract;

    /**
     * Sets HTTP method to callable binding (multiple version).
     *
     * @param MethodsContract $methods a HTTP methods contract
     */
    public function setMethods(MethodsContract $methods): RouteContract;

    public function setId(string $id): RouteContract;

    public function addMiddleware(string $callable): RouteContract;

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
     * @param string $set route set, null to use $this->set ?? $this->uri
     */
    // FIXME: Don't pass null
    public function regex(?string $set = null): string;
}
