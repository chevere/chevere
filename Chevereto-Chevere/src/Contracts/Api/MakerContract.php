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

namespace Chevere\Contracts\Api;

use Chevere\Cache\Cache;
use Chevere\Path\PathHandle;
use Chevere\Router\Maker as RouterMaker;

interface MakerContract
{
    public function __construct(RouterMaker $router);

    public function withPathHandle(PathHandle $pathHandle): MakerContract;

    public function withCache(): MakerContract;

    public function hasApi(): bool;

    public function hasPathHandle(): bool;

    public function hasCache(): bool;

    public function api(): array;

    public function pathHandle(): PathHandle;

    public function cache(): Cache;
}
