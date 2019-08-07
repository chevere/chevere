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

namespace Chevere\Contracts\Router;

use Chevere\Contracts\Route\RouteContract;

interface RouterContract
{
    public function addRoute(RouteContract $route, string $basename);

    public function getRegex(): string;

    /**
     * Resolve routing for the given path info, sets matched arguments.
     *
     * @param string $pathInfo request path
     */
    public function resolve(string $pathInfo): RouteContract;
}
