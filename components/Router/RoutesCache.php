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
use Chevere\Components\VarExportable\VarExportable;
use Throwable;

final class RoutesCache implements RoutesCacheInterface
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function has(string $name): bool
    {
        return $this->cache->exists(new CacheKey($name));
    }

    public function get(string $name): RouteInterface
    {
        try {
            $item = $this->cache->get(new CacheKey($name));
        } catch (Throwable $e) {
            throw new RouteCacheNotFoundException(
                (new Message('Cache not found for route %routeName%'))
                    ->strong('%routeName%', $name)
                    ->toString()
            );
        }

        return $item->var();
    }

    public function put(RouteInterface $route): void
    {
        $this->cache = $this->cache
            ->withPut(
                new CacheKey($route->name()->toString()),
                new VarExportable($route)
            );
    }

    public function remove(string $name): void
    {
        $this->cache = $this->cache
            ->withRemove(
                new CacheKey($name)
            );
    }

    public function puts(): array
    {
        return $this->cache->puts();
    }
}
