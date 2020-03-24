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

interface RoutesCacheInterface
{
    public function has(string $routeName): bool;

    public function get(string $routeName): RouteInterface;

    public function put(RouteInterface $route): void;

    public function remove(string $routeName): void;

    public function puts(): array;
}
