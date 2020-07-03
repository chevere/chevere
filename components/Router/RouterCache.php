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

use Chevere\Components\Cache\Cache;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Message\Message;
use Chevere\Components\VarExportable\VarExportable;
use Chevere\Exceptions\Router\RouterCacheNotFoundException;
use Chevere\Interfaces\Cache\CacheInterface;
use Chevere\Interfaces\Cache\CacheItemInterface;
use Chevere\Interfaces\Cache\CacheKeyInterface;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RouterCacheInterface;
use Chevere\Interfaces\Router\RouterIndexInterface;
use Chevere\Interfaces\Router\RouterInterface;
use Chevere\Interfaces\Router\RouterRegexInterface;
use Chevere\Interfaces\Router\RoutesCacheInterface;
use Throwable;

final class RouterCache implements RouterCacheInterface
{
    private CacheInterface $cache;

    private RoutesCacheInterface $routesCache;

    private RouteResolvesCache $routeResolvesCache;

    private CacheKeyInterface $keyRegex;

    private CacheKeyInterface $keyIndex;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->routesCache = new RoutesCache(
            new Cache($this->cache->dir()->getChild('routes/'))
        );
        $this->routeResolvesCache = new RouteResolvesCache(
            new Cache($this->cache->dir()->getChild('resolve/'))
        );
        $this->keyRegex = new CacheKey(self::KEY_REGEX);
        $this->keyIndex = new CacheKey(self::KEY_INDEX);
    }

    public function routesCache(): RoutesCacheInterface
    {
        return $this->routesCache;
    }

    public function routeResolvesCache(): RouteResolvesCache
    {
        return $this->routeResolvesCache;
    }

    public function hasRegex(): bool
    {
        return $this->cache->exists($this->keyRegex);
    }

    public function hasIndex(): bool
    {
        return $this->cache->exists($this->keyIndex);
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

    public function withPut(RouterInterface $router): RouterCacheInterface
    {
        $new = clone $this;
        $new->cache = $new->cache
            ->withAddedItem(
                $new->keyRegex,
                new VarExportable($router->regex())
            )
            ->withAddedItem(
                $new->keyIndex,
                new VarExportable($router->index())
            );
        $pos = -1;
        /**
         * @var RoutableInterface $routable
         */
        foreach ($router->routables()->mapCopy() as $routable) {
            $route = $routable->route();
            $pos++;
            $new->routesCache->put($route);
            $new->routeResolvesCache->put(
                $pos,
                new RouteResolve(
                    $route->name(),
                    $route->path()->wildcards(),
                )
            );
        }

        return $new;
    }

    public function remove(): void
    {
        $this->cache = $this->cache
            ->withoutItem($this->keyRegex)
            ->withoutItem($this->keyIndex);
        foreach (array_keys($this->routesCache->puts()) as $routeName) {
            $this->routesCache->remove($routeName);
        }
    }

    public function puts(): array
    {
        return $this->cache->puts();
    }

    private function assertGetItem(CacheKeyInterface $cacheKey): CacheItemInterface
    {
        try {
            $item = $this->cache->get($cacheKey);
        } catch (Throwable $e) {
            throw new RouterCacheNotFoundException(
                (new Message('Cache not found for router %key%'))
                    ->strong('%key%', $cacheKey->toString())
            );
        }

        return $item;
    }
}
