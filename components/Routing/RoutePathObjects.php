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
use SplObjectStorage;

final class RoutePathObjects extends SplObjectStorage
{
    public function append(RoutePathInterface $routePath, RouteDecoratorInterface $routeDecorator)
    {
        return parent::attach($routePath, $routeDecorator);
    }

    public function current(): RoutePathInterface
    {
        return parent::current();
    }

    public function routeDecorator(): RouteDecoratorInterface
    {
        return parent::getInfo();
    }
}
