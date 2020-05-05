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

namespace Chevere\Components\Routing;

use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;

final class DecoratedRoute
{
    private RoutePathInterface $path;

    private RouteDecoratorInterface $decorator;

    public function __construct(RoutePathInterface $path, RouteDecoratorInterface $decorator)
    {
        $this->path = $path;
        $this->decorator = $decorator;
    }

    public function routePath(): RoutePathInterface
    {
        return $this->path;
    }

    public function routeDecorator(): RouteDecoratorInterface
    {
        return $this->decorator;
    }
}
