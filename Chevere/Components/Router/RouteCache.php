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
use Chevere\Components\Router\Exceptions\RouteCacheTypeException;
use Chevere\Components\Router\Interfaces\RouteCacheInterface;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Type\Type;
use Chevere\Components\Variable\VariableExport;
use Throwable;

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
        $idString = (string) $id;
        try {
            $item = $this->cache->get(new CacheKey($idString));
        } catch (Throwable $e) {
            throw new RouteCacheNotFoundException(
                (new Message('Cache not found for route %id%'))
                    ->strong('%id%', $idString)
                    ->toString()
            );
        }
        if ((new Type(RouteableInterface::class))->validate($item->var()) === false) {
            throw new RouteCacheTypeException(
                (new Message('Expecting object implementing %expected%, %provided% provided in route %id%'))
                    ->code('%expected%', RouteableInterface::class)
                    ->code('%provided%', gettype($item->raw()))
                    ->strong('%id%', $idString)
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
