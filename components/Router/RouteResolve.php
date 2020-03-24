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

namespace Chevere\Components\Router;

use Chevere\Components\Route\RouteWildcards;

final class RouteResolve
{
    private string $name;

    private RouteWildcards $routeWildcards;

    public function __construct(string $name, RouteWildcards $routeWildcards)
    {
        $this->name = $name;
        $this->routeWildcards = $routeWildcards;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function routeWildcards(): RouteWildcards
    {
        return $this->routeWildcards;
    }
}
