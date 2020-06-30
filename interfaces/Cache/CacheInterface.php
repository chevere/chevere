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

namespace Chevere\Interfaces\Cache;

use Chevere\Exceptions\Cache\CacheKeyNotFoundException;
use Chevere\Exceptions\Filesystem\DirUnableToCreateException;
use Chevere\Exceptions\Filesystem\FileUnableToRemoveException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\VarExportable\VarExportableInterface;

interface CacheInterface
{
    const ILLEGAL_KEY_CHARACTERS = '\.\/\\\~\:';

    public function __construct(DirInterface $dir);

    /**
     * Put item in cache.
     *
     * Return an instance with the specified CacheKeyInterface put.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified CacheKeyInterface VarExportableInterface.
     *
     * @param CacheKeyInterface $cacheKey Cache key
     * @param VarExportableInterface $varExportable an export variable
     */
    public function withPut(CacheKeyInterface $cacheKey, VarExportableInterface $varExportable): CacheInterface;

    /**
     * Remove item from cache.
     *
     * Return an instance with the specified CacheKeyInterface removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified CacheKeyInterface removed.
     *
     * @param CacheKeyInterface $cacheKey Cache key
     * @throws FileUnableToRemoveException if unable to remove the cache file
     */
    public function withRemove(CacheKeyInterface $cacheKey): CacheInterface;

    /**
     * Returns a boolean indicating whether the cache exists for the given key.
     */
    public function exists(CacheKeyInterface $cacheKey): bool;

    /**
     * Get a cache item.
     *
     * @throws CacheKeyNotFoundException
     */
    public function get(CacheKeyInterface $cacheKey): CacheItemInterface;

    /**
     * Provides access to the array containing puts.
     *
     * ```php
     * return [
     *      'key' => [
     *              'checksum' => '<file_checksum>',
     *              'path' => '<the_file_path>'
     *      ],
     * ];
     * ```
     */
    public function puts(): array;

    /**
     * Proxy for DirInterface getChild.
     *
     * @throws DirUnableToCreateException
     */
    public function getChild(string $path): CacheInterface;
}
