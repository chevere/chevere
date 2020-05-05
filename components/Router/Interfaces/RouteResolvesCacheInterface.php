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

use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Router\RouteResolve;

interface RouteResolvesCacheInterface
{
    public function __construct(CacheInterface $cache);

    public function has(int $id): bool;

    public function get(int $id): RouteResolve;

    public function put(int $id, RouteResolve $routeResolve): void;

    public function remove(int $id): void;

    public function puts(): array;
}
