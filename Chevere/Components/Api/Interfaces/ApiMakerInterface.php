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

namespace Chevere\Components\Api\Interfaces;

use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;

interface ApiMakerInterface
{
    public function __construct(RouterMaker $router);

    public function withPath(PathInterface $path): ApiMakerInterface;

    public function withCache(CacheInterface $cache): ApiMakerInterface;

    public function hasApi(): bool;

    public function hasPath(): bool;

    public function hasCache(): bool;

    public function api(): array;

    public function path(): PathInterface;

    public function cache(): CacheInterface;
}
