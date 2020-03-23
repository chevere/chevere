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
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Router\Exceptions\RouteCacheNotFoundException;
use Chevere\Components\Router\Interfaces\RoutesCacheInterface;
use Chevere\Components\Variable\VariableExport;
use Throwable;

final class RoutesCache implements RoutesCacheInterface
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

    public function get(string $routeName): RouteInterface
    {
        try {
            $item = $this->cache->get(new CacheKey($routeName));
        } catch (Throwable $e) {
            throw new RouteCacheNotFoundException(
                (new Message('Cache not found for route %routeName%'))
                    ->strong('%routeName%', $routeName)
                    ->toString()
            );
        }

        return $item->var();
    }

    public function put(RouteInterface $route): RoutesCache
    {
        $this->cache = $this->cache
            ->withPut(
                new CacheKey($route->name()->toString()),
                new VariableExport($route)
            );

        return $this;
    }

    public function remove(string $routeName): RoutesCacheInterface
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
