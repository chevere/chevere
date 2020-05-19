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

namespace Chevere\Components\Route;

use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\Interfaces\RouteWildcardsInterface;

final class RouteDecorator implements RouteDecoratorInterface
{
    private RouteNameInterface $name;

    private RouteWildcardsInterface $wildcards;

    public function __construct(RouteNameInterface $name)
    {
        $this->name = $name;
        $this->wildcards = new RouteWildcards;
    }

    public function withWildcards(RouteWildcardsInterface $wildcards): RouteDecoratorInterface
    {
        $new = clone $this;
        $new->wildcards = $wildcards;

        return $new;
    }

    public function name(): RouteNameInterface
    {
        return $this->name;
    }

    public function wildcards(): RouteWildcardsInterface
    {
        return $this->wildcards;
    }
}
