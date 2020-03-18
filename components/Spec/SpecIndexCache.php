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

use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Spec\Interfaces\SpecIndexCacheInterface;
use Chevere\Components\Spec\Interfaces\SpecIndexInterface;
use Chevere\Components\Variable\VariableExport;

// Add this header to all responses: Link: </spec/api/routes.json>; rel="describedby"
final class SpecIndexCache implements SpecIndexCacheInterface
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function has(int $id): bool
    {
        $cacheKey = new CacheKey((string) $id);

        return $this->cache->exists($cacheKey);
    }

    public function get(int $id): SpecMethods
    {
        $cacheKey = new CacheKey((string) $id);

        return $this->cache->get($cacheKey)->var();
    }

    public function put(SpecIndexInterface $specIndex): void
    {
        /**
         * @var int $routeId
         * @var SpecMethods
         */
        foreach ($specIndex->specIndexMap()->map() as $routeId => $specMethods) {
            $this->cache->withPut(
                new CacheKey((string) $routeId),
                new VariableExport($specMethods)
            );
        }
    }
}
