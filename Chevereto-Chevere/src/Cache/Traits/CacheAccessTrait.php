<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Cache\Traits;

use Chevere\Cache\Cache;

trait CacheAccessTrait
{
    /** @var Cache */
    private $cache;

    public function hasCache(): bool
    {
        return isset($this->cache);
    }

    public function cache(): Cache
    {
        return $this->cache;
    }
}
