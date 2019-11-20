<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Contracts\Router;

use Chevere\Contracts\Cache\CacheContract;
use Chevere\Contracts\Route\RouteContract;

interface RouterContract
{
    const CACHE_ID = 'router';

    public function withRouterMaker(RouterMakerContract $maker): RouterContract;

    public function withCache(CacheContract $cache): RouterContract;

    public function hasMaker(): bool;

    public function hasCache(): bool;

    public function routerMaker(): RouterMakerContract;

    public function cache(): CacheContract;

    public function arguments(): array;

    public function canResolve(): bool;

    public function resolve(string $pathInfo): RouteContract;
}
