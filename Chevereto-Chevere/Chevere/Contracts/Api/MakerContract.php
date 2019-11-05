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

namespace Chevere\Contracts\Api;

use Chevere\Components\Cache\Cache;
use Chevere\Components\Router\Maker as RouterMaker;
use Chevere\Contracts\Path\PathContract;

interface MakerContract
{
    public function __construct(RouterMaker $router);

    public function withPath(PathContract $path): MakerContract;

    public function withCache(Cache $cache): MakerContract;

    public function hasApi(): bool;

    public function hasPath(): bool;

    public function hasCache(): bool;

    public function api(): array;

    public function path(): PathContract;

    public function cache(): Cache;
}
