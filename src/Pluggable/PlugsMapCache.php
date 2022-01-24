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

namespace Chevere\Pluggable;

use Chevere\Cache\Cache;
use Chevere\Cache\CacheKey;
use Chevere\Cache\Interfaces\CacheInterface;
use Chevere\Cache\Interfaces\CacheKeyInterface;
use Chevere\ClassMap\ClassMap;
use Chevere\ClassMap\Interfaces\ClassMapInterface;
use function Chevere\Filesystem\filePhpReturnForPath;
use Chevere\Message\Message;
use Chevere\Pluggable\Interfaces\PlugsMapCacheInterface;
use Chevere\Pluggable\Interfaces\PlugsMapInterface;
use Chevere\Pluggable\Interfaces\PlugsQueueTypedInterface;
use Chevere\Str\Str;
use Chevere\Throwable\Exception;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\RuntimeException;
use Chevere\VarSupport\VarStorable;
use ReflectionClass;
use Throwable;

final class PlugsMapCache implements PlugsMapCacheInterface
{
    /**
     * @var ClassMapInterface [pluggableClassName => path_to_plugsQueue,]
     */
    private ClassMapInterface $classMap;

    private CacheKeyInterface $classMapKey;

    public function __construct(
        private CacheInterface $cache
    ) {
        $this->classMapKey = new CacheKey(self::KEY_CLASS_MAP);
    }

    public function withPut(PlugsMapInterface $plugsMap): PlugsMapCacheInterface
    {
        $new = clone $this;
        $new->classMap = new ClassMap();

        foreach ($plugsMap->getIterator() as $pluggableName => $plugsQueueTyped) {
            $classNameAsPath = (new Str($pluggableName))
                    ->withForwardSlashes()->__toString() . '/';
            $cacheAt = new Cache($new->cache->dir()->getChild($classNameAsPath));
            $queueName = (new ReflectionClass($plugsQueueTyped))->getShortName();
            $cacheAt = $cacheAt->withPut(
                new CacheKey($queueName),
                new VarStorable($plugsQueueTyped)
            );
            $new->classMap = $new->classMap
                    ->withPut(
                        $pluggableName,
                        $cacheAt->puts()[$queueName]['path']
                    );
        }
        $new->cache = $new->cache
                ->withPut(
                    $new->classMapKey,
                    new VarStorable($new->classMap)
                );

        return $new;
    }

    public function hasPlugsQueueTypedFor(string $className): bool
    {
        if (!$this->cache->exists($this->classMapKey)) {
            return false;
        }

        try {
            return $this->getClassMapFromCache()->has($className);
        } catch (Throwable $e) {
            return false;
        }
    }

    public function getPlugsQueueTypedFor(string $className): PlugsQueueTypedInterface
    {
        $this->assertClassMap();
        $classMapCached = $this->getClassMapFromCache();
        if (!$classMapCached->has($className)) {
            throw new OutOfBoundsException(
                (new Message('Class name %className% not found'))
                    ->code('%className%', $className),
                3
            );
        }

        try {
            $path = $classMapCached->key($className);

            return filePhpReturnForPath($path)->var();
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new RuntimeException(
                previous: $e,
                message: (new Message('Unable to retrieve cached variable for %className% cache at path %path%'))
                    ->code('%className%', $className)
                    ->code('%path%', $path ?? '<unmapped>'),
            );
        }
        // @codeCoverageIgnoreEnd
    }

    private function getClassMapFromCache(): ClassMapInterface
    {
        try {
            $var = $this->cache->get($this->classMapKey);

            return $var->var();
        }
        // @codeCoverageIgnoreStart
        catch (Exception $e) {
            throw new RuntimeException(
                previous: $e,
                message: (new Message('Unable to retrieve cache for key %key%'))
                    ->code('%key%', $this->classMapKey->__toString()),
            );
        }
        // @codeCoverageIgnoreEnd
    }

    private function assertClassMap(): void
    {
        if (!$this->cache->exists($this->classMapKey)) {
            throw new OutOfBoundsException(
                (new Message('No cache exists at cache key %key%'))
                    ->code('%key%', $this->classMapKey->__toString()),
                1
            );
        }
    }
}
