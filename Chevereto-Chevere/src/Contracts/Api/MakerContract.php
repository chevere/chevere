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

    public static function create(PathHandle $pathHandle, RouterMaker $routerMaker): MakerContract;

    public function api(): array;

    public function withCache(): MakerContract;

    public function cache(): Cache;
}
