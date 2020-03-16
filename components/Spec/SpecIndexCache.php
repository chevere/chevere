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

namespace Chevere\Components\Spec;

use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Spec\Interfaces\SpecIndexCacheInterface;
use Chevere\Components\Spec\Interfaces\SpecIndexInterface;

// Add this header to all responses: Link: </spec/api/routes.json>; rel="describedby"
final class SpecIndexCache implements SpecIndexCacheInterface
{
    private CacheInterface $cache;

    private array $array;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function put(SpecIndexInterface $spec): void
    {
    }
}
