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

use Chevere\Components\Cache\Exceptions\CacheKeyNotFoundException;
use Chevere\Interfaces\Cache\CacheInterface;
use Chevere\Interfaces\Cache\CacheItemInterface;
use Chevere\Interfaces\Cache\CacheKeyInterface;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Components\Message\Message;
use Chevere\Interfaces\VarExportable\VarExportableInterface;
use Chevere\Components\Filesystem\Exceptions\DirUnableToCreateException;

/**
 * A simple PHP based cache system.
 *
 * Using FileReturn, it provides cache by using php files that return a single variable.
 *
 * cached.php >>> <?php return 'my cached data';
 */
final class Cache implements CacheInterface
{
    /** @var DirInterface */
    private DirInterface $dir;

    /** @var array An array [key => [checksum => , path =>]] containing information about the cache items */
    private array $puts;

    /**
     * @param DirInterface $dir the directory where cache files will be stored/accessed (must exists)
     * @throws DirUnableToCreateException if $dir doesn't exists and unable to create
     */
    public function __construct(DirInterface $dir)
    {
        $this->dir = $dir;
        if ($this->dir->exists() === false) {
            $this->dir->create(); // @codeCoverageIgnore
        }
        $this->puts = [];
    }

    public function withPut(CacheKeyInterface $key, VarExportableInterface $varExportable): CacheInterface
    {
        $path = $this->getPath($key->toString());
        $file = new File($path);
        if (!$file->exists()) {
            $file->create();
        }
        $filePhp = new FilePhp($file);
        $fileReturn = new FilePhpReturn($filePhp);
        $fileReturn->put($varExportable);
        $filePhp->cache();
        $new = clone $this;
        $new->puts[$key->toString()] = [
            'path' => $fileReturn->filePhp()->file()->path()->absolute(),
            'checksum' => $fileReturn->filePhp()->file()->checksum(),
        ];

        return $new;
    }

    public function withRemove(CacheKeyInterface $cacheKey): CacheInterface
    {
        $new = clone $this;
        $path = $this->getPath($cacheKey->toString());
        if ($path->exists() === false) {
            return $new; // @codeCoverageIgnore
        }
        $filePhp = new FilePhp(new File($path));
        $filePhp->flush();
        $filePhp->file()->remove();
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

    public function getChild(string $path): CacheInterface
    {
        return new self($this->dir->getChild($path));
    }

    private function getPath(string $name): PathInterface
    {
        return $this->dir->path()
            ->getChild($name . '.php');
    }
}
