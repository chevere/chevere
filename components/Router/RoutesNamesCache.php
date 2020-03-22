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

namespace Chevere\Components\Router;

use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Router\Interfaces\RoutesNamesCacheInterface;
use Chevere\Components\Variable\VariableExport;

/**
 * @codeCoverageIgnore
 */
final class RoutesNamesCache implements RoutesNamesCacheInterface
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function has(string $routeName): bool
    {
        return $this->cache->exists(new CacheKey($routeName));
    }

    public function get(int $id): string
    {
        return $this->cache->get(new CacheKey((string) $id))->raw();
    }

    public function put(int $id, string $routeName): RoutesNamesCache
    {
        $this->cache = $this->cache
            ->withPut(
                new CacheKey((string) $id),
                new VariableExport($routeName)
            );

        return $this;
    }

    public function remove(string $routeName): RoutesNamesCache
    {
        $this->cache = $this->cache
            ->withRemove(
                new CacheKey($routeName)
            );

        return $this;
    }

    public function puts(): array
    {
        return $this->cache->puts();
    }
}
