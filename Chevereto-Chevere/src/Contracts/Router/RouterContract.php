<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Contracts\Router;

use Chevere\Cache\Cache;
use Chevere\Contracts\Route\RouteContract;

interface RouterContract
{
    public function withMaker(MakerContract $maker): RouterContract;

    public function withCache(Cache $cache): RouterContract;

    public function hasMaker(): bool;

    public function hasCache(): bool;

    public function maker(): MakerContract;

    public function cache(): Cache;

    public function arguments(): array;

    public function resolve(string $pathInfo): RouteContract;
}
