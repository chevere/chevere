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

use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use function DeepCopy\deep_copy;

final class Router implements RouterInterface
{
    private Routeables $routeables;

    private RouterRegexInterface $routerRegex;

    private RouterIndexInterface $routerIndex;

    public function __construct()
    {
        $this->routeables = new Routeables;
        $this->routerIndex = new RouterIndex;
    }

    public function withRouteables(Routeables $routeables): RouterInterface
    {
        $new = clone $this;
        $new->routeables = $routeables;

        return $new;
    }

    public function routeables(): Routeables
    {
        return deep_copy($this->routeables);
    }

    public function withRegex(RouterRegexInterface $regex): RouterInterface
    {
        $new = clone $this;
        $new->routerRegex = $regex;

        return $new;
    }

    public function hasRegex(): bool
    {
        return isset($this->routerRegex);
    }

    public function regex(): RouterRegexInterface
    {
        return $this->routerRegex;
    }

    public function withIndex(RouterIndexInterface $index): RouterInterface
    {
        $new = clone $this;
        $new->routerIndex = $index;

        return $new;
    }

    public function index(): RouterIndexInterface
    {
        return $this->routerIndex;
    }
}
