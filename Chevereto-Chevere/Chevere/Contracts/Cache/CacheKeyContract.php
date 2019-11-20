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

use Chevere\Components\Cache\Exceptions\CacheInvalidKeyException;

interface CacheKeyContract
{
    const ILLEGAL_KEY_CHARACTERS = '\.\/\\\~\:';

    /**
     * @param string $key Cache key entry
     *
     * @throws CacheInvalidKeyException if $name contains illegal characters
     */
    public function __construct(string $key);

    /**
     * Get the cache key string.
     */
    public function toString(): string;
}
