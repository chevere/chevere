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

namespace Chevere\Components\Plugs;

use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Exceptions\CacheKeyNotFoundException;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Cache\Interfaces\CacheKeyInterface;
use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\ClassMap\Interfaces\ClassMapInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Interfaces\PlugsMapInterface;
use Chevere\Components\Plugs\Interfaces\PlugsRegistryInterface;
use Chevere\Components\Str\Str;
use Chevere\Components\VarExportable\VarExportable;
use LogicException;
use TypeError;

final class PlugsRegistry implements PlugsRegistryInterface
{
    private ClassMap $classMap;

    private CacheInterface $cache;

    private CacheKeyInterface $classMapKey;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->classMapKey = new CacheKey('ClassMap');
    }

    public function withAddedClassMap(CacheKeyInterface $key, PlugsMapInterface $plugsMap): PlugsRegistryInterface
    {
        $new = clone $this;
        $new->classMap = new ClassMap;
        $cache = $new->getCache($key);
        foreach ($plugsMap->getGenerator() as $pluggableName => $queue) {
            $classNameAsPath = (new Str($pluggableName))->forwardSlashes()->toString();
            $cacheAt = $cache->getChild($classNameAsPath . '/');
            $queueName = $queue->plugType()->queueName();
            $cacheAt = $cacheAt->withPut(new CacheKey($queueName), new VarExportable($queue));
            $new->classMap = $new->classMap
                ->withPut(
                    $pluggableName,
                    $cacheAt->puts()[$queueName]['path']
                );
        }
        $cache
            ->withPut(
                $new->classMapKey,
                new VarExportable($new->classMap)
            );

        return $new;
    }

    public function hasClassMap(CacheKeyInterface $key): bool
    {
        return $this->getCache($key)->exists($this->classMapKey);
    }

    /**
     * @throws DirUnableToCreateException
     */
    public function getClassMap(CacheKeyInterface $key): ClassMapInterface
    {
        $cache = $this->getCache($key);
        try {
            $return = $cache->get($this->classMapKey);
        } catch (CacheKeyNotFoundException $e) {
            throw new LogicException(
                (new Message('No cached class map for key %key%'))
                    ->code('%key%', $key->toString())
                    ->toString()
            );
        }
        try {
            return $return->var();
        } catch (TypeError $e) {
            die('eeeeeeeeeeeeeeeeeeeeee');
        }
    }

    /**
     * @throws DirUnableToCreateException
     */
    private function getCache(CacheKeyInterface $key): CacheInterface
    {
        return $this->cache->getChild($key->toString() . '/');
    }
}
