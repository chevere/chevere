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

use Chevere\Exceptions\Cache\CacheInvalidKeyException;
use Chevere\Interfaces\To\ToStringInterface;

/**
 * Describes the component in charge of defining a cache key.
 */
interface CacheKeyInterface extends ToStringInterface
{
    public const ILLEGAL_KEY_CHARACTERS = '\.\/\\\~\:';

    /**
     * @throws CacheInvalidKeyException
     */
    public function __construct(string $key);

    /**
     * Provides access to `$key`.
     */
    public function toString(): string;
}
