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
use Chevere\Exceptions\Cache\CacheInvalidKeyException;
use Chevere\Exceptions\Cache\CacheKeyNotFoundException;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Filesystem\DirUnableToCreateException;
use Chevere\Exceptions\Filesystem\PathInvalidException;
use Chevere\Exceptions\Filesystem\PathIsDirException;
use Chevere\Interfaces\Cache\CacheInterface;
use Chevere\Interfaces\Cache\CacheItemInterface;
use Chevere\Interfaces\Cache\CacheKeyInterface;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Interfaces\VarExportable\VarExportableInterface;

final class Cache implements CacheInterface
{
    private DirInterface $dir;

    /** @var array An array [key => [checksum => , path =>]] containing information about the cache items */
    private array $puts;

    /**
     * @throws DirUnableToCreateException
     */
    public function __construct(DirInterface $dir)
    {
        $this->dir = $dir;
        if ($this->dir->exists() === false) {
            $this->dir->create(); // @codeCoverageIgnore
        }
        $this->puts = [];
    }

    public function dir(): DirInterface
    {
        return $this->dir;
    }

    /**
     * @throws OutOfBoundsException
     * @throws RuntimeException If theres an system failure
     */
    public function withAddedItem(CacheKeyInterface $key, VarExportableInterface $varExportable): CacheInterface
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
            $fileReturn->put($varExportable);
            $filePhp->cache();
            $new = clone $this;
            $new->puts[$key->toString()] = [
                'path' => $fileReturn->filePhp()->file()->path()->absolute(),
                'checksum' => $fileReturn->filePhp()->file()->checksum(),
            ];
        } catch (Exception $e) {
            throw new RuntimeException(
                $e->message(),
                $e->getCode(),
                $e
            );
        }

        return $new;
    }

    /**
     * @throws CacheInvalidKeyException
     * @throws RuntimeException
     */
    public function withoutItem(CacheKeyInterface $cacheKey): CacheInterface
    {
        $new = clone $this;
        $path = $this->getPath($cacheKey->toString());
        try {
            if ($path->exists() === false) {
                return $new; // @codeCoverageIgnore
            }
            $filePhp = new FilePhp(new File($path));
            $filePhp->flush();
            $filePhp->file()->remove();
        } catch (Exception $e) {
            throw new RuntimeException(
                $e->message(),
                $e->getCode(),
                $e
            );
        }
        unset($new->puts[$cacheKey->toString()]);

        return $new;
    }

    /**
     * @throws OutOfBoundsException
     */
    public function exists(CacheKeyInterface $cacheKey): bool
    {
        return $this->getPath($cacheKey->toString())->exists();
    }

    /**
     * @throws OutOfBoundsException
     * @throws CacheKeyNotFoundException
     */
    public function get(CacheKeyInterface $cacheKey): CacheItemInterface
    {
        $path = $this->getPath($cacheKey->toString());
        if (!$path->exists()) {
            throw new CacheKeyNotFoundException(
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

    /**
     * @throws OutOfBoundsException
     */
    private function getPath(string $name): PathInterface
    {
        try {
            return $this->dir->path()->getChild($name . '.php');
        } catch (PathIsDirException | PathInvalidException $e) {
            throw new OutOfBoundsException(
                $e->message(),
                $e->getCode(),
                $e
            );
        }
    }
}
