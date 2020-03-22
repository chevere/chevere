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
use Chevere\Components\Router\Exceptions\RouterCacheTypeException;
use Chevere\Components\Router\Interfaces\RouterCacheInterface;
use Chevere\Components\Router\Interfaces\RouterGroupsInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Interfaces\RouterNamedInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use Chevere\Components\Router\Interfaces\RoutesCacheInterface;
use Chevere\Components\Variable\VariableExport;
use Throwable;

final class RouterCache implements RouterCacheInterface
{
    private CacheInterface $cache;

    private RoutesCacheInterface $routesCache;

    private CacheKeyInterface $keyRegex;

    private CacheKeyInterface $keyIndex;

    private CacheKeyInterface $keyGroups;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->keyRegex = new CacheKey(self::KEY_REGEX);
        $this->keyIndex = new CacheKey(self::KEY_INDEX);
        $this->keyGroups = new CacheKey(self::KEY_GROUPS);
    }

    public function routesCache(): RoutesCacheInterface
    {
        return $this->routesCache ??= new RoutesCache($this->cache->getChild('routes/'));
    }

    public function hasRegex(): bool
    {
        return $this->cache->exists($this->keyRegex);
    }

    public function hasIndex(): bool
    {
        return $this->cache->exists($this->keyIndex);
    }

    public function hasGroups(): bool
    {
        return $this->cache->exists($this->keyGroups);
    }

    public function getRegex(): RouterRegexInterface
    {
        $item = $this->assertGetItem(
            $this->keyRegex,
            RouterRegexInterface::class
        );

        return $item->var();
    }

    public function getIndex(): RouterIndexInterface
    {
        $item = $this->assertGetItem(
            $this->keyIndex,
            RouterIndexInterface::class
        );

        return $item->var();
    }

    public function getGroups(): RouterGroupsInterface
    {
        $item = $this->assertGetItem(
            $this->keyGroups,
            RouterGroupsInterface::class
        );

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
                $this->keyGroups,
                new VariableExport($router->groups())
            );
        $routes = $router->routeables();
        foreach ($routes->map() as $routeName => $route) {
            $this->routesCache()->put($route);
        }

        return $this;
    }

    public function remove(): RouterCacheInterface
    {
        $this->cache = $this->cache
            ->withRemove($this->keyRegex)
            ->withRemove($this->keyIndex)
            ->withRemove($this->keyGroups);
        foreach (array_keys($this->routesCache()->puts()) as $routeName) {
            $this->routesCache()->remove($routeName);
        }

        return $this;
    }

    public function puts(): array
    {
        return $this->cache->puts();
    }

    private function assertGetItem(CacheKeyInterface $cacheKey, string $interfaceName): CacheItemInterface
    {
        try {
            $item = $this->cache->get($cacheKey);
        } catch (Throwable $e) {
            throw new RouterCacheNotFoundException(
                (new Message('Cache not found for router %key%'))
                    ->strong('%key%', $cacheKey->toString())
                    ->toString()
            );
        }
        if (!is_object($item)) {
            throw new RouterCacheTypeException(
                (new Message('Expecting a cached object, type %type% found'))
                    ->code('%type%', gettype($item))
                    ->toString()
            );
        }

        return $item;
    }
}
