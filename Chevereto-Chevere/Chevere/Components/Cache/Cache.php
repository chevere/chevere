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
use Chevere\Components\Message\Message;
use Chevere\Contracts\Cache\CacheContract;
use Chevere\Contracts\Cache\CacheKeyContract;
use Chevere\Contracts\Dir\DirContract;
use Chevere\Contracts\File\FileReturnContract;
use Chevere\Contracts\Path\PathContract;

/**
 * A simple PHP based cache system.
 *
 * Using FileReturn, it provides cache by using php files that return a single variable.
 *
 * cached.php >>> <?php return 'my cached data';
 */
final class Cache implements CacheContract
{
    /** @var string Cache name */
    private $name;

    /** @var DirContract */
    private $dir;

    /** @var array An array [key => [checksum => , path =>]] containing information about the cache instance */
    private $array;

    /**
     * {@inheritdoc}
     */
    public function __construct(CacheKeyContract $cacheKey, DirContract $dir)
    {
        $this->name = $cacheKey->get();
        $this->dir = $dir;
        if (!$this->dir->path()->exists()) {
            $this->dir->create();
        }
        $this->assertIsDirectory();
        $this->array = [];
    }

    /**
     * {@inheritdoc}
     */
    public function withPut(CacheKeyContract $cacheKey, $var): CacheContract
    {
        $path = $this->getPath(
            $cacheKey->get()
        );
        $file = new File($path);
        if (!$file->exists()) {
            $file->create();
        }
        $filePhp = new FilePhp($file);
        $fileReturn = new FileReturn($filePhp);
        $fileReturn->put($var);
        new FileCompile($filePhp);
        $new = clone $this;
        $new->array[$new->name][$cacheKey->get()] = [
            'path' => $fileReturn->file()->path()
                ->absolute(),
            'checksum' => $fileReturn->checksum(),
        ];

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(CacheKeyContract $cacheKey): bool
    {
        return $this->getPath($cacheKey->get())
            ->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function get(CacheKeyContract $cacheKey): FileReturnContract
    {
        $path = $this->getPath($cacheKey->get());
        if (!$path->exists()) {
            throw new CacheKeyNotFoundException('No cache for key ' . $key);
        }

        return new FileReturn(
            new FilePhp(
                new File($path)
            )
        );
    }

    // public function remove(string $key): void
    // {
    //     $path = $this->getPath($key);
    //     if (!$path->exists()) {
    //         return;
    //     }
    //     (new File($path))->remove();
    //     unset($this->array[$this->name][$key]);
    // }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->array;
    }

    private function getPath(string $name): PathContract
    {
        return $this->dir->path()
            ->getChild($name . '.php');
    }

    private function assertIsDirectory(): void
    {
        if (!$this->dir->path()->exists()) {
            throw new PathIsNotDirectoryException(
                (new Message('Path %path% is not a directory'))
                    ->code('%path%', $this->dir->path()->absolute())
                    ->toString()
            );
        }
    }
}
