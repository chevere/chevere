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
use Chevere\Components\Path\Exceptions\PathIsNotDirectoryException;
use Chevere\Components\Cache\Exceptions\CacheKeyNotFoundException;

interface CacheContract
{
    const ILLEGAL_KEY_CHARACTERS = '\.\/\\\~\:';

    /**
     * Creates a new instance.
     *
     * @param DirContract $dir the directory where cache files will be stored/accesed (must exists)
     *
     * @throws PathIsNotDirectoryException if the DirContract doesn't represent an existing directory
     */
    public function __construct(DirContract $dir);

    /**
     * Put cache.
     *
     * @param string $key Cache key
     * @param mixed  $var anything, but keep it restricted to one-dimension iterables at most
     */
    public function withPut(CacheKeyContract $cacheKey, $var): CacheContract;

    /**
     * Returns a boolean indicating whether the key exists in the cache.
     */
    public function exists(CacheKeyContract $cacheKey): bool;

    /**
     * Get cache as a FileReturn object.
     *
     * @return FileReturnContract for the cache file
     *
     * @throws CacheKeyNotFoundException If the cache key doesn't exists
     */
    public function get(CacheKeyContract $cacheKey): FileReturnContract;

    /**
     * Gets a resume of the cached entries.
     */
    public function toArray(): array;
}
