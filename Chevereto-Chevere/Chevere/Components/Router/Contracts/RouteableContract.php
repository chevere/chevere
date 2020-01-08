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

namespace Chevere\Components\Router\Contracts;

use Chevere\Components\Route\Contracts\RouteContract;
use Chevere\Components\Router\Exceptions\RouteableException;

interface RouteableContract
{
    /**
     * Creates a new instance.
     *
     * @throws RouteableException if $route is not routeable
     */
    public function __construct(RouteContract $route);

    /**
     * Provides access to the RouteContract instance.
     */
    public function route(): RouteContract;
}
