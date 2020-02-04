<?php

namespace Chevere\Components\Router;

use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Router\Exceptions\RouteCacheTypeException;
use Chevere\Components\Router\Interfaces\RouteCacheInterface;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Type\Type;
use Chevere\Components\Variable\VariableExport;

final class RouteCache implements RouteCacheInterface
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function has(int $id): bool
    {
        return $this->cache->exists(new CacheKey((string) $id));
    }

    public function get(int $id): RouteInterface
    {
        $item = $this->cache->get(new CacheKey((string) $id));
        if ((new Type(RouteableInterface::class))->validate($item->var()) === false) {
            throw new RouteCacheTypeException(
                (new Message('Expecting object implementing %expected%, %provided% provided in route %id%'))
                    ->code('%expected%', RouteableInterface::class)
                    ->code('%provided%', gettype($item->raw()))
                    ->strong('%id%', $id)
                    ->toString()
            );
        }

        return $item->var()->route();
    }

    public function put(int $id, RouteableInterface $routeable): RouteCache
    {
        $this->cache = $this->cache
            ->withPut(
                new CacheKey((string) $id),
                new VariableExport($routeable)
            );

        return $this;
    }

    public function remove(int $id): RouteCacheInterface
    {
        $this->cache = $this->cache
            ->withRemove(
                new CacheKey((string) $id)
            );

        return $this;
    }

    public function puts(): array
    {
        return $this->cache->puts();
    }
}
