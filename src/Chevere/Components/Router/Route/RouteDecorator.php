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

namespace Chevere\Components\Router\Route;

use Chevere\Interfaces\Router\Route\RouteDecoratorInterface;
use Chevere\Interfaces\Router\Route\RouteLocatorInterface;
use Chevere\Interfaces\Router\Route\WildcardsInterface;

final class RouteDecorator implements RouteDecoratorInterface
{
    private RouteLocatorInterface $name;

    private WildcardsInterface $wildcards;

    public function __construct(RouteLocatorInterface $name)
    {
        $this->name = $name;
        $this->wildcards = new Wildcards();
    }

    public function withWildcards(WildcardsInterface $wildcards): RouteDecoratorInterface
    {
        $new = clone $this;
        $new->wildcards = $wildcards;

        return $new;
    }

    public function locator(): RouteLocatorInterface
    {
        return $this->name;
    }

    public function wildcards(): WildcardsInterface
    {
        return $this->wildcards;
    }
}
