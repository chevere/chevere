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
use Chevere\Components\Regex\Interfaces\RegexInterface;
use Chevere\Components\Router\Exceptions\RouterCacheNotFoundException;
use Chevere\Components\Router\Exceptions\RouterCacheTypeException;
use Chevere\Components\Router\Interfaces\RouterCacheInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\Type\Type;
use Chevere\Components\Variable\VariableExport;
use Throwable;

final class RouterCache implements RouterCacheInterface
{
    private CacheInterface $cache;

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
        $this->keyRegex = new CacheKey(self::KEY_REGEX);
        $this->keyIndex = new CacheKey(self::KEY_INDEX);
        $this->keyNamed = new CacheKey(self::KEY_NAMED);
        $this->keyGroups = new CacheKey(self::KEY_GROUPS);
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

    public function getRegex(): RegexInterface
    {
        $item = $this->assertGetItem($this->keyRegex);
        if ((new Type(RegexInterface::class))->validate($item->var()) === false) {
            throw new RouterCacheTypeException(
                (new Message('Expecting object implementing %expected%, %provided% provided for %key%'))
                    ->code('%expected%', RegexInterface::class)
                    ->code('%provided%', gettype($item->raw()))
                    ->strong('%key%', $this->keyRegex->toString())
                    ->toString()
            );
        }

        return $item->var();
    }

    public function getIndex(): array
    {
        $item = $this->assertGetItem($this->keyIndex);
        $this->assertItemIsArray($item);

        return $item->raw();
    }

    public function getNamed(): array
    {
        $item = $this->assertGetItem($this->keyNamed);
        $this->assertItemIsArray($item);

        return $item->raw();
    }

    public function getGroups(): array
    {
        $item = $this->assertGetItem($this->keyGroups);
        $this->assertItemIsArray($item);

        return $item->raw();
    }

    public function put(RouterInterface $router): RouterCacheInterface
    {
        foreach ([
            [$this->keyRegex, new VariableExport($router->regex())],
            [$this->keyIndex, new VariableExport($router->index())],
            [$this->keyNamed, new VariableExport($router->named())],
            [$this->keyGroups, new VariableExport($router->groups())],
        ] as $pos => $args) {
            $this->cache = $this->cache
                ->withPut(...$args);
        }

        return $this;
    }

    public function puts(): array
    {
        return $this->cache->puts();
    }

    private function assertGetItem(CacheKeyInterface $cacheKey): CacheItemInterface
    {
        try {
            return $this->cache->get($this->keyRegex);
        } catch (Throwable $e) {
            throw new RouterCacheNotFoundException(
                (new Message('Cache not found for router %key%'))
                    ->strong('%key%', $cacheKey->toString())
                    ->toString()
            );
        }
    }

    private function assertItemIsArray(CacheItemInterface $item): void
    {
        if ((new Type(TypeInterface::ARRAY))->validate($item->raw()) === false) {
            throw new RouterCacheTypeException(
                (new Message('Expecting type %expected%, type %provided% provided'))
                    ->code('%expected%', 'array')
                    ->code('%provided%', gettype($item->raw()))
                    ->toString()
            );
        }
    }
}
