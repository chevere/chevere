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
use Chevere\Components\Cache\Exceptions\CacheKeyNotFoundException;
use Chevere\Contracts\Variable\VariableExportContract;
use Chevere\Components\File\Exceptions\FileUnableToRemoveException;

interface CacheContract
{
    const ILLEGAL_KEY_CHARACTERS = '\.\/\\\~\:';

    /**
     * Creates a new instance.
     *
     * @param DirContract $dir the directory where cache files will be stored/accesed (must exists)
     *
     * @throws DirUnableToCreateException if $dir doesn't exists and unable to create
     */
    public function __construct(DirContract $dir);

    /**
     * Put item in cache.
     *
     * Return an instance with the specified CacheKeyContract put.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified CacheKeyContract VariableExportContract.
     *
     * @param CacheKeyContract       $cacheKey       Cache key
     * @param VariableExportContract $variableExport an export variable
     */
    public function withPut(CacheKeyContract $cacheKey, VariableExportContract $variableExport): CacheContract;

    /**
     * Remove item from cache.
     *
     * Return an instance with the specified CacheKeyContract removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified CacheKeyContract removed.
     *
     * @param CacheKeyContract $cacheKey Cache key
     *
     * @throws FileUnableToRemoveException if unable to remove the cache file
     */
    public function withRemove(CacheKeyContract $cacheKey): CacheContract;

    /**
     * Returns a boolean indicating whether the alleged key exists in the cache.
     */
    public function exists(CacheKeyContract $cacheKey): bool;

    /**
     * Get a cache item.
     *
     * @throws CacheKeyNotFoundException If the cache key doesn't exists
     */
    public function get(CacheKeyContract $cacheKey): CacheItemContract;

    /**
     * Gets a resume of the cached entries.
     */
    public function toArray(): array;
}
