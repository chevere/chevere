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

interface RouterCacheContract
{
    public function __construct(CacheContract $cache);

    public function cache(): CacheContract;

    /**
     * Return an instance with the specified CacheContract.
     *
     * Cacheable properties of this instance will be cached On the target CacheContract (put).
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified CacheContract.
     */
    public function withPut(RouterMakerContract $routerMaker): RouterCacheContract;

    // public function get(): CacheItemContract;
}
