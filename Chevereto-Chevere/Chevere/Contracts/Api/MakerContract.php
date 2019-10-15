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
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Maker as RouterMaker;

interface MakerContract
{
    public function __construct(RouterMaker $router);

    public function withPath(Path $pat): MakerContract;

    public function withCache(): MakerContract;

    public function hasApi(): bool;

    public function hasPath(): bool;

    public function hasCache(): bool;

    public function api(): array;

    public function path(): Path;

    public function cache(): Cache;
}
