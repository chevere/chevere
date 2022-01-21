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
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Filesystem\DirUnableToCreateException;
use Chevere\Interfaces\Cache\CacheInterface;
use Chevere\Interfaces\Cache\CacheItemInterface;
use Chevere\Interfaces\Cache\CacheKeyInterface;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Interfaces\VarSupport\VarStorableInterface;
use Throwable;

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
        $path = $this->getPath($key->__toString());

        try {
            $file = new File($path);
            if (!$file->exists()) {
                $file->create();
            }
            $filePhp = new FilePhp($file);
            $fileReturn = new FilePhpReturn($filePhp);
            $fileReturn->put($var);
            // @infection-ignore-all
            $filePhp->cache();
            $new = clone $this;
            $new->puts[$key->__toString()] = [
                'path' => $fileReturn->filePhp()->file()->path()->__toString(),
                'checksum' => $fileReturn->filePhp()->file()->getChecksum(),
            ];
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new RuntimeException(previous: $e);
        }
        // @codeCoverageIgnoreEnd

        return $new;
    }

    /**
     * @infection-ignore-all
     */
    public function without(CacheKeyInterface $cacheKey): CacheInterface
    {
        $new = clone $this;
        $path = $this->getPath($cacheKey->__toString());

        try {
            if (!$path->exists()) {
                // @codeCoverageIgnoreStart
                return $new;
                // @codeCoverageIgnoreEnd
            }
            $filePhp = new FilePhp(new File($path));
            // @infection-ignore-all
            $filePhp->flush();
            $filePhp->file()->remove();
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (Throwable $e) {
            throw new RuntimeException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        unset($new->puts[$cacheKey->__toString()]);

        return $new;
    }

    public function exists(CacheKeyInterface $cacheKey): bool
    {
        return $this->getPath($cacheKey->__toString())->exists();
    }

    public function get(CacheKeyInterface $cacheKey): CacheItemInterface
    {
        $path = $this->getPath($cacheKey->__toString());
        if (!$path->exists()) {
            throw new OutOfBoundsException(
                (new Message('No cache for key %key%'))
                    ->code('%key%', $cacheKey->__toString())
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

        return $this->dir->path()->getChild($child);
    }
}
