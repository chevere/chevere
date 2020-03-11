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

namespace Chevere\Components\Router\Interfaces;

use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\RoutePath;

/**
 * Provides access to the router index.
 */
interface RouterIndexInterface
{
    public function withAdded(RouteInterface $route, int $id, string $group): RouterIndexInterface;

    public function has(RoutePath $routePath): bool;

    public function get(RoutePath $routePath): RouteIdentifierInterface;

    public function toArray(): array;
}
