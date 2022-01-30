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

use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Stringable;

/**
 * Describes the component in charge of defining a cache key.
 */
interface CacheKeyInterface extends Stringable
{
    public const ILLEGAL_KEY_CHARACTERS = '\.\/\\\~\:';

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $key);

    /**
     * Provides access to `$key`.
     */
    public function __toString(): string;
}
