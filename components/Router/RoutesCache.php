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
use Chevere\Components\Message\Message;
use Chevere\Components\VarExportable\VarExportable;
use Chevere\Exceptions\Core\RangeException;
use Chevere\Exceptions\Router\RouteCacheNotFoundException;
use Chevere\Interfaces\Cache\CacheInterface;
use Chevere\Interfaces\Route\RouteInterface;
use Chevere\Interfaces\Router\RoutesCacheInterface;
use Throwable;
use TypeError;

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
            );
        }

        return $item->var();
    }

    public function put(RouteInterface $route): void
    {
        $this->cache = $this->cache
            ->withAddedItem(
                new CacheKey($route->name()->toString()),
                new VarExportable($route)
            );
    }

    public function remove(string $name): void
    {
        $this->cache = $this->cache
            ->withoutItem(
                new CacheKey($name)
            );
    }

    public function puts(): array
    {
        return $this->cache->puts();
    }
}
