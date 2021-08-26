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

namespace Chevere\Components\Cache;

use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Filesystem\DirUnableToCreateException;
use Chevere\Interfaces\Cache\CacheInterface;
use Chevere\Interfaces\Cache\CacheItemInterface;
use Chevere\Interfaces\Cache\CacheKeyInterface;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Interfaces\Var\VarStorableInterface;

final class Cache implements CacheInterface
{
    /**
     * @var array An array [key => [checksum => , path =>]] containing information about the cache items
     */
    private array $puts;

    /**
     * @throws DirUnableToCreateException
     */
    public function __construct(
        private DirInterface $dir
    ) {
        if (!$this->dir->exists()) {
            // @codeCoverageIgnore
            $this->dir->create();
        }
        $this->puts = [];
    }

    public function dir(): DirInterface
    {
        return $this->dir;
    }

    public function withPut(CacheKeyInterface $key, VarStorableInterface $var): CacheInterface
    {
        $path = $this->getPath($key->toString());

        try {
            $file = new File($path);
            if (!$file->exists()) {
                $file->create();
            }
            $file->assertExists();
            $filePhp = new FilePhp($file);
            $fileReturn = new FilePhpReturn($filePhp);
            $fileReturn->put($var);
            $filePhp->cache();
            $new = clone $this;
            $new->puts[$key->toString()] = [
                'path' => $fileReturn->filePhp()->file()->path()->toString(),
                'checksum' => $fileReturn->filePhp()->file()->getChecksum(),
            ];
        }
        // @codeCoverageIgnoreStart
        catch (Exception $e) {
            throw new RuntimeException(previous: $e);
        }
        // @codeCoverageIgnoreEnd

        return $new;
    }

    public function without(CacheKeyInterface $cacheKey): CacheInterface
    {
        $new = clone $this;
        $path = $this->getPath($cacheKey->toString());

        try {
            if (!$path->exists()) {
                // @codeCoverageIgnoreStart
                return $new;
                // @codeCoverageIgnoreEnd
            }
            $filePhp = new FilePhp(new File($path));
            $filePhp->flush();
            $filePhp->file()->remove();
        }
        // @codeCoverageIgnoreStart
        catch (Exception $e) {
            throw new RuntimeException(
                $e->message(),
            );
        }
        // @codeCoverageIgnoreEnd
        unset($new->puts[$cacheKey->toString()]);

        return $new;
    }

    public function exists(CacheKeyInterface $cacheKey): bool
    {
        return $this->getPath($cacheKey->toString())->exists();
    }

    public function get(CacheKeyInterface $cacheKey): CacheItemInterface
    {
        $path = $this->getPath($cacheKey->toString());
        if (!$path->exists()) {
            throw new OutOfBoundsException(
                (new Message('No cache for key %key%'))
                    ->code('%key%', $cacheKey->toString())
            );
        }

        return new CacheItem(
            new FilePhpReturn(
                new FilePhp(
                    new File($path)
                )
            )
        );
    }

    public function puts(): array
    {
        return $this->puts;
    }

    private function getPath(string $name): PathInterface
    {
        $child = $name . '.php';

        try {
            return $this->dir->path()->getChild($child);
        }
        // @codeCoverageIgnoreStart
        catch (Exception $e) {
            throw new RuntimeException(
                previous: $e,
                message: (new Message('Unable to get cache for child %child% at path %path%'))
                    ->code('%child%', $child)
                    ->code('%path%', $this->dir->path()->toString()),
            );
        }
        // @codeCoverageIgnoreEnd
    }
}
