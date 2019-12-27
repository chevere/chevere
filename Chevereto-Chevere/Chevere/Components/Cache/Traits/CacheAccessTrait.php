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

namespace Chevere\Components\Cache\Traits;

use Chevere\Contracts\Cache\CacheContract;

trait CacheAccessTrait
{
    private ?CacheContract $cache;

    public function hasCache(): bool
    {
        return isset($this->cache);
    }

    public function cache(): CacheContract
    {
        return $this->cache;
    }
}
