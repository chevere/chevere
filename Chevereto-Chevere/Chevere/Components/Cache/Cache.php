<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Cache;

use Chevere\Components\Cache\Exceptions\CacheKeyNotFoundException;
use Chevere\Components\File\File;
use Chevere\Components\File\FileCompile;
use Chevere\Components\File\FilePhp;
use Chevere\Components\File\FileReturn;
use Chevere\Contracts\Cache\CacheContract;
use Chevere\Contracts\Cache\CacheItemContract;
use Chevere\Contracts\Cache\CacheKeyContract;
use Chevere\Contracts\Dir\DirContract;
use Chevere\Contracts\Path\PathContract;
use Chevere\Contracts\Variable\VariableExportContract;

/**
 * A simple PHP based cache system.
 *
 * Using FileReturn, it provides cache by using php files that return a single variable.
 *
 * cached.php >>> <?php return 'my cached data';
 */
final class Cache implements CacheContract
{
    /** @var DirContract */
    private DirContract $dir;

    /** @var array An array [key => [checksum => , path =>]] containing information about the cache items */
    private array $puts;

    /**
     * {@inheritdoc}
     */
    public function __construct(DirContract $dir)
    {
        $this->dir = $dir;
        if (!$this->dir->exists()) {
            $this->dir->create();
        }
        $this->puts = [];
    }

    /**
     * {@inheritdoc}
     */
    public function withPut(CacheKeyContract $cacheKey, VariableExportContract $variableExport): CacheContract
    {
        $path = $this->getPath($cacheKey->toString());
        $file = new File($path);
        if (!$file->exists()) {
            $file->create();
        }
        $filePhp = new FilePhp($file);
        $fileReturn = new FileReturn($filePhp);
        $fileReturn->put($variableExport);
        new FileCompile($filePhp);
        $new = clone $this;
        $new->puts[$cacheKey->toString()] = [
            'path' => $fileReturn->filePhp()->file()->path()->absolute(),
            'checksum' => $fileReturn->filePhp()->file()->checksum(),
        ];

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withRemove(CacheKeyContract $cacheKey): CacheContract
    {
        $new = clone $this;
        $path = $this->getPath($cacheKey->toString());
        if (!$path->exists()) {
            return $new;
        }
        $fileCompile =
            new FileCompile(
                new FilePhp(
                    new File($path)
                )
            );
        $fileCompile->destroy();
        $fileCompile->filePhp()->file()->remove();

        unset($new->puts[$cacheKey->toString()]);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(CacheKeyContract $cacheKey): bool
    {
        return $this->getPath($cacheKey->toString())
            ->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function get(CacheKeyContract $cacheKey): CacheItemContract
    {
        $path = $this->getPath($cacheKey->toString());
        if (!$path->exists()) {
            throw new CacheKeyNotFoundException('No cache for key ' . $cacheKey->toString());
        }

        return
            new CacheItem(
                new FileReturn(
                    new FilePhp(
                        new File($path)
                    )
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->puts;
    }

    private function getPath(string $name): PathContract
    {
        return $this->dir->path()
            ->getChild($name . '.php');
    }
}
