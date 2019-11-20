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
    const KEY_REGEX = 'regex';
    const KEY_ROUTES = 'routes';
    const KEY_INDEX = 'index';

    /**
     * Creates a new instance.
     */
    public function __construct(CacheContract $cache);

    /**
     * Provides access to the CacheContract instance.
     */
    public function cache(): CacheContract;

    /**
     * Return an instance with the cache put values of RouterMakerContract.
     *
     * Cacheable properties of this instance will be cached On the target CacheContract (put).
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the put cache for the values of RouterMakerContract.
     */
    public function withPut(RouterMakerContract $routerMaker): RouterCacheContract;
}
