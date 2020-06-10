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

use Chevere\Interfaces\Router\RouterIndexInterface;
use Chevere\Interfaces\Router\RouterInterface;
use Chevere\Interfaces\Router\RouterRegexInterface;
use function DeepCopy\deep_copy;

final class Router implements RouterInterface
{
    private Routables $routables;

    private RouterRegexInterface $routerRegex;

    private RouterIndexInterface $routerIndex;

    public function __construct()
    {
        $this->routables = new Routables;
        $this->routerIndex = new RouterIndex;
    }

    public function withRoutables(Routables $routables): RouterInterface
    {
        $new = clone $this;
        $new->routables = $routables;

        return $new;
    }

    public function routables(): Routables
    {
        return deep_copy($this->routables);
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
