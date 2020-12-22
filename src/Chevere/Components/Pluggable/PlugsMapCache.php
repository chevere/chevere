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

namespace Chevere\Components\Pluggable;

use Chevere\Components\Cache\Cache;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\ClassMap\ClassMap;
use function Chevere\Components\Filesystem\filePhpReturnForPath;
use Chevere\Components\Message\Message;
use Chevere\Components\Str\Str;
use Chevere\Components\VarExportable\VarExportable;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Cache\CacheInterface;
use Chevere\Interfaces\Cache\CacheKeyInterface;
use Chevere\Interfaces\ClassMap\ClassMapInterface;
use Chevere\Interfaces\Pluggable\PlugsMapCacheInterface;
use Chevere\Interfaces\Pluggable\PlugsMapInterface;
use Chevere\Interfaces\Pluggable\PlugsQueueTypedInterface;
use ReflectionClass;
use Throwable;

final class PlugsMapCache implements PlugsMapCacheInterface
{
    /**
     * @var ClassMapInterface [pluggableClassName => path_to_plugsQueue,]
     */
    private ClassMapInterface $classMap;

    private CacheInterface $cache;

    private CacheKeyInterface $classMapKey;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->classMapKey = new CacheKey(self::KEY_CLASS_MAP);
    }

    public function withPut(PlugsMapInterface $plugsMap): PlugsMapCacheInterface
    {
        $new = clone $this;
        $new->classMap = new ClassMap();

        try {
            foreach ($plugsMap->getGenerator() as $pluggableName => $plugsQueueTyped) {
                $classNameAsPath = (new Str($pluggableName))
                    ->withForwardSlashes()->toString() . '/';
                $cacheAt = new Cache($new->cache->dir()->getChild($classNameAsPath));
                $queueName = (new ReflectionClass($plugsQueueTyped))->getShortName();
                $cacheAt = $cacheAt->withPut(
                    new CacheKey($queueName),
                    new VarExportable($plugsQueueTyped)
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
                    new VarExportable($new->classMap)
                );
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new RuntimeException(
                (new Message('Unable to put provided plugs map')),
                0,
                $e
            );
        }
        // @codeCoverageIgnoreEnd

        return $new;
    }

    public function hasPlugsQueueTypedFor(string $className): bool
    {
        if (! $this->cache->exists($this->classMapKey)) {
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
        $classMap = $this->getClassMapFromCache();
        if (! $classMap->has($className)) {
            throw new OutOfBoundsException(
                (new Message('Class name %className% not found'))
                    ->code('%className%', $className),
                3
            );
        }

        try {
            $path = $classMap->key($className);

            return filePhpReturnForPath($path)->withStrict(false)->var();
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new RuntimeException(
                (new Message('Unable to retrieve cached variable for %className% cache at path %path%'))
                    ->code('%className%', $className)
                    ->code('%path%', $path ?? '<unmapped>'),
                0,
                $e
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
                (new Message('Unable to retrieve cache for key %key%'))
                    ->code('%key%', $this->classMapKey->toString()),
                0,
                $e
            );
        }
        // @codeCoverageIgnoreEnd
    }

    private function assertClassMap(): void
    {
        if (! $this->cache->exists($this->classMapKey)) {
            throw new OutOfBoundsException(
                (new Message('No cache exists at cache key %key%'))
                    ->code('%key%', $this->classMapKey->toString()),
                1
            );
        }
    }
}
