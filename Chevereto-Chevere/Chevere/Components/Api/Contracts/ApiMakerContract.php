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

namespace Chevere\Components\Api\Contracts;

use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Cache\Contracts\CacheContract;
use Chevere\Components\Path\Contracts\PathContract;

interface ApiMakerContract
{
    public function __construct(RouterMaker $router);

    public function withPath(PathContract $path): ApiMakerContract;

    public function withCache(CacheContract $cache): ApiMakerContract;

    public function hasApi(): bool;

    public function hasPath(): bool;

    public function hasCache(): bool;

    public function api(): array;

    public function path(): PathContract;

    public function cache(): CacheContract;
}
