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

namespace Chevere\Components\Cache\Interfaces;

use Chevere\Components\Common\Interfaces\ToStringInterface;

interface CacheKeyInterface extends ToStringInterface
{
    const ILLEGAL_KEY_CHARACTERS = '\.\/\\\~\:';

    public function __construct(string $key);

    /**
     * @return string cache key string.
     */
    public function toString(): string;
}
