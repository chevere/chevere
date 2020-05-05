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
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Router\Interfaces\RouterCacheInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use Chevere\Components\Router\Interfaces\RoutesCacheInterface;
use Chevere\Components\VarExportable\VarExportable;
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
        $this->routesCache = new RoutesCache($this->cache->getChild('routes/'));
        $this->routeResolvesCache = new RouteResolvesCache(
            $this->cache->getChild('resolve/')
        );
        $this->keyRegex = new CacheKey(self::KEY_REGEX);
        $this->keyIndex = new CacheKey(self::KEY_INDEX);
    }

    public function routesCache(): RoutesCacheInterface
    {
        return $this->routesCache;
    }

    public function resolverCache(): RouteResolvesCache
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

    public function put(RouterInterface $router): void
    {
        $this->cache = $this->cache
            ->withPut(
                $this->keyRegex,
                new VarExportable($router->regex())
            )
            ->withPut(
                $this->keyIndex,
                new VarExportable($router->index())
            );
        $pos = -1;
        /**
         * @var RouteableInterface $routeable
         */
        foreach ($router->routeables()->map() as $routeable) {
            $route = $routeable->route();
            $pos++;
            $this->routesCache->put($route);
            $this->routeResolvesCache->put(
                $pos,
                new RouteResolve(
                    $route->name(),
                    $route->path()->wildcards()
                )
            );
        }
    }

    public function remove(): void
    {
        $this->cache = $this->cache
            ->withRemove($this->keyRegex)
            ->withRemove($this->keyIndex);
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
