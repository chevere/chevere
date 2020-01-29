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

namespace Chevere\Components\Cache;

use Chevere\Components\Cache\Exceptions\CacheInvalidKeyException;
use Chevere\Components\Message\Message;
use Chevere\Components\Cache\Interfaces\CacheKeyInterface;

final class CacheKey implements CacheKeyInterface
{
    /** @var string */
    private string $key;

    /**
     * @param string $key Cache key entry
     *
     * @throws CacheInvalidKeyException if $name contains illegal characters
     */
    public function __construct(string $key)
    {
        $this->key = $key;
        $this->assertKeyName();
    }

    public function toString(): string
    {
        return $this->key;
    }

    private function assertKeyName(): void
    {
        if (preg_match_all('#[' . CacheKeyInterface::ILLEGAL_KEY_CHARACTERS . ']#', $this->key, $matches)) {
            $matches = array_unique($matches[0]);
            $forbidden = implode(' ', $matches);
            throw new CacheInvalidKeyException(
                (new Message('Use of forbidden character(s) %character%'))
                    ->code('%character%', $forbidden)
                    ->toString()
            );
        }
    }
}
