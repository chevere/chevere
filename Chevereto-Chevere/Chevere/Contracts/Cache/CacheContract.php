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

namespace Chevere\Contracts\Cache;

use Chevere\Contracts\Dir\DirContract;
use Chevere\Contracts\File\FileReturnContract;

interface CacheContract
{
    const ILLEGAL_KEY_CHARACTERS = '\.\/\\\~\:';

    /**
     * @param string      $name Named cache entry (folder)
     * @param DirContract $dir  The directory where cache files will be stored/accesed
     *
     * @throws CacheInvalidKeyException    if $name contains illegal characters
     * @throws PathIsNotDirectoryException if the DirContract doesn't represent an existing directory
     */
    public function __construct(CacheKeyContract $cacheKey, DirContract $dir);

    /**
     * Put cache.
     *
     * @param string $key Cache key
     * @param mixed  $var anything, but keep it restricted to one-dimension iterables at most
     *
     * @throws CacheInvalidKeyException if $key contains illegal characters
     */
    public function withPut(CacheKeyContract $cacheKey, $var): CacheContract;

    /**
     * Returns a boolean indicating whether the cache key exists.
     *
     * @throws CacheInvalidKeyException if $key contains illegal characters
     */
    public function exists(CacheKeyContract $cacheKey): bool;

    /**
     * Get cache as a FileReturn object.
     *
     * @return FileReturnContract for the cache file
     *
     * @throws CacheInvalidKeyException  if $key contains illegal characters
     * @throws CacheKeyNotFoundException If the cache key doesn't exists
     */
    public function get(CacheKeyContract $cacheKey): FileReturnContract;

    /**
     * Gets a resume of the cached entries.
     */
    public function toArray(): array;
}
