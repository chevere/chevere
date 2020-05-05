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

namespace Chevere\Components\Routing\Interfaces;

use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;

interface DecoratedRouteInterface
{
    public function __construct(RoutePathInterface $path, RouteDecoratorInterface $decorator);

    public function routePath(): RoutePathInterface;

    public function routeDecorator(): RouteDecoratorInterface;
}
