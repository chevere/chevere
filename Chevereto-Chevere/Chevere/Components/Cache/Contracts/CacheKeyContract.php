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

namespace Chevere\Components\Cache\Contracts;

use Chevere\Components\Cache\Exceptions\CacheInvalidKeyException;
use Chevere\Components\Common\Contracts\ToStringContract;

interface CacheKeyContract extends ToStringContract
{
    const ILLEGAL_KEY_CHARACTERS = '\.\/\\\~\:';

    /**
     * @param string $key Cache key entry
     *
     * @throws CacheInvalidKeyException if $name contains illegal characters
     */
    public function __construct(string $key);

    /**
     * @return string cache key string.
     */
    public function toString(): string;
}
