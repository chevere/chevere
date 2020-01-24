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

namespace Chevere\Components\Router\Interfaces;

use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Cache\Exceptions\CacheNotFoundException;

interface RouterCacheInterface
{
    const KEY_REGEX = 'regex';
    const KEY_ROUTES = 'routes';
    const KEY_INDEX = 'index';

    public function __construct(CacheInterface $cache);

    /**
     * Provides access to the CacheInterface instance.
     */
    public function cache(): CacheInterface;

    /**
     * Return an instance with the cache put values of RouterMakerInterface.
     *
     * Cacheable properties of this instance will be cached On the target CacheInterface (put).
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the put cache for the values of RouterMakerInterface.
     */
    public function withPut(RouterMakerInterface $routerMaker): RouterCacheInterface;

    /**
     * Gets router properties from cache.
     *
     * @throws CacheNotFoundException if unable to locate the cache
     */
    public function getProperties(): RouterPropertiesInterface;
}
