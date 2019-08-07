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

interface RouteContract
{
    public function __construct(string $uri, string $controller = null);

    public function setName(string $name): RouteContract;

    public function setWhere(string $wildcardName, string $regex): RouteContract;

    public function setWheres(array $wildcardsPatterns): RouteContract;

    public function setMethod(string $httpMethod, string $controller): RouteContract;

    public function setMethods(array $httpMethodsCallables): RouteContract;

    public function setId(string $id): RouteContract;

    public function addMiddleware(string $callable): RouteContract;

    public function getController(string $httpMethod): string;

    public function fill(): RouteContract;

    // FIXME: Don't pass null
    public function regex(?string $set = null): string;
}
