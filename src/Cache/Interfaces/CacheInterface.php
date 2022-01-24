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

namespace Chevere\Cache\Interfaces;

use Chevere\Filesystem\Exceptions\DirUnableToCreateException;
use Chevere\Filesystem\Exceptions\FileUnableToRemoveException;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\VarSupport\Interfaces\VarStorableInterface;

/**
 * Describes the component in charge of caching PHP variables.
 *
 * `cached.php >>> <?php return 'my cached data';`
 */
interface CacheInterface
{
    public const ILLEGAL_KEY_CHARACTERS = '\.\/\\\~\:';

    /**
     * @param DirInterface $dir Directory for working cache
     * @throws DirUnableToCreateException if $dir doesn't exists and unable to create
     */
    public function __construct(DirInterface $dir);

    /**
     * Provides access to the cache directory.
     */
    public function dir(): DirInterface;

    /**
     * Put item in cache.
     *
     * Return an instance with the specified put.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified put.
     */
    public function withPut(CacheKeyInterface $key, VarStorableInterface $var): self;

    /**
     * Remove item from cache.
     *
     * Return an instance with the specified removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified removed.
     *
     * @throws FileUnableToRemoveException if unable to remove the cache file
     */
    public function without(CacheKeyInterface $key): self;

    /**
     * Indicates whether the cache exists for the given key.
     */
    public function exists(CacheKeyInterface $key): bool;

    /**
     * Get a cache item.
     *
     * @throws OutOfBoundsException
     */
    public function get(CacheKeyInterface $key): CacheItemInterface;

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
}
