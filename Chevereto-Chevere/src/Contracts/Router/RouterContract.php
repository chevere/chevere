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

use Chevere\Router\Maker;
use Chevere\Contracts\Route\RouteContract;

interface RouterContract
{
    public function __construct();

    public function arguments(): array;

    /**
     * Resolve routing for the given path info, sets matched arguments.
     *
     * @param string $pathInfo request path
     */
    public function resolve(string $pathInfo): RouteContract;
}
