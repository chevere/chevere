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
use Chevere\Components\Router\RoutesNamesCache;

interface RoutesNamesCacheInterface
{
    public function has(string $routeName): bool;

    public function get(int $id): RouteInterface;

    public function put(int $id, string $routeName): RoutesNamesCache;

    public function remove(string $routeName): RoutesNamesCache;

    public function puts(): array;
}
