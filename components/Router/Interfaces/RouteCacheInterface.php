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

interface RouteCacheInterface
{
    public function has(int $id): bool;

    public function get(int $id): RouteInterface;

    public function put(int $id, RouteableInterface $routeable): RouteCacheInterface;

    public function remove(int $id): RouteCacheInterface;

    public function puts(): array;
}
