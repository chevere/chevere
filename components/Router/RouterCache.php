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
use Chevere\Components\Cache\Interfaces\CacheItemInterface;
use Chevere\Components\Cache\Interfaces\CacheKeyInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exceptions\RouterCacheNotFoundException;
use Chevere\Components\Router\Interfaces\RouteCacheInterface;
use Chevere\Components\Router\Interfaces\RouterCacheInterface;
use Chevere\Components\Router\Interfaces\RouterGroupsInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RouterNamedInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use Chevere\Components\Variable\VariableExport;
use Throwable;

final class RouterCache implements RouterCacheInterface
{
    private CacheInterface $cache;

    private RouteCacheInterface $routeCache;

    private CacheKeyInterface $keyRegex;

    private CacheKeyInterface $keyIndex;

    private CacheKeyInterface $keyNamed;

    private CacheKeyInterface $keyGroups;

    /**
     * Creates a new instance.
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->routeCache = new RouteCache($this->cache->getChild('routes'));
        $this->keyRegex = new CacheKey(self::KEY_REGEX);
        $this->keyIndex = new CacheKey(self::KEY_INDEX);
        $this->keyNamed = new CacheKey(self::KEY_NAMED);
        $this->keyGroups = new CacheKey(self::KEY_GROUPS);
    }

    public function routeCache(): RouteCacheInterface
    {
        return $this->routeCache;
    }

    public function hasRegex(): bool
    {
        return $this->cache->exists($this->keyRegex);
    }

    public function hasIndex(): bool
    {
        return $this->cache->exists($this->keyIndex);
    }

    public function hasNamed(): bool
    {
        return $this->cache->exists($this->keyNamed);
    }

    public function hasGroups(): bool
    {
        return $this->cache->exists($this->keyGroups);
    }

    public function getRegex(): RouterRegexInterface
    {
        $item = $this->assertGetItem($this->keyRegex);

        return $item->var();
    }

    public function getIndex(): RouterIndexInterface
    {
        $item = $this->assertGetItem($this->keyIndex);

        return $item->var();
    }

    public function getNamed(): RouterNamedInterface
    {
        $item = $this->assertGetItem($this->keyNamed);

        return $item->var();
    }

    public function getGroups(): RouterGroupsInterface
    {
        $item = $this->assertGetItem($this->keyGroups);

        return $item->var();
    }

    public function put(RouterInterface $router): RouterCacheInterface
    {
        $this->cache = $this->cache
            ->withPut(
                $this->keyRegex,
                new VariableExport($router->regex())
            )
            ->withPut(
                $this->keyIndex,
                new VariableExport($router->index())
            )
            ->withPut(
                $this->keyNamed,
                new VariableExport($router->named())
            )
            ->withPut(
                $this->keyGroups,
                new VariableExport($router->groups())
            );

        return $this;
    }

    public function remove(): RouterCacheInterface
    {
        $this->cache = $this->cache
            ->withRemove($this->keyRegex)
            ->withRemove($this->keyIndex)
            ->withRemove($this->keyNamed)
            ->withRemove($this->keyGroups);

        return $this;
    }

    public function puts(): array
    {
        return $this->cache->puts();
    }

    private function assertGetItem(CacheKeyInterface $cacheKey): CacheItemInterface
    {
        try {
            return $this->cache->get($cacheKey);
        } catch (Throwable $e) {
            throw new RouterCacheNotFoundException(
                (new Message('Cache not found for router %key%'))
                    ->strong('%key%', $cacheKey->toString())
                    ->toString()
            );
        }
    }
}
