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

use InvalidArgumentException;
use LogicException;
use Chevere\Components\Dir\Dir;
use Chevere\Components\File\File;
use Chevere\Components\File\FileCompile;
use Chevere\Components\File\FilePhp;
use Chevere\Components\File\FileReturn;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Cache\CacheContract;
use Chevere\Contracts\Dir\DirContract;
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
     * @param string $name Named cache entry (folder)
     * @param Dir    $dir  The directory where cache files will be stored/accesed
     */
    public function __construct(string $name, Dir $dir)
    {
        $this->assertKeyName($name);
        $this->name = $name;
        $this->dir = $dir;
        if (!$this->dir->path()->exists()) {
            $this->dir->create();
        }
        if (!$this->dir->path()->exists()) {
            throw new InvalidArgumentException(
                (new Message('Path %path% is not a directory'))
                    ->code('%path%', $this->dir->path()->absolute())
                    ->toString()
            );
        }
        $this->array = [];
    }

    /**
     * Get cache as a FileReturn object.
     *
     * @return FileReturn a FileReturn instance for the cache file
     */
    public function get(string $key): FileReturn
    {
        $path = $this->getPath($key);
        if (!$path->exists()) {
            throw new LogicException('No cache for key ' . $key);
        }

        return new FileReturn(
            new FilePhp(
                new File($path)
            )
        );
    }

    public function exists(string $key): bool
    {
        return $this->getPath($key)
            ->exists();
    }

    /**
     * Put cache.
     *
     * @param string $key Cache key
     * @param mixed  $var anything, but keep it restricted to one-dimension iterables at most
     */
    public function withPut(string $key, $var): CacheContract
    {
        $path = $this->getPath($key);
        $file = new File($path);
        if (!$file->exists()) {
            $file->create();
        }
        $filePhp = new FilePhp($file);
        $fileReturn = new FileReturn($filePhp);
        $fileReturn->put($var);
        new FileCompile($filePhp);
        $new = clone $this;
        $new->array[$new->name][$key] = [
            'path' => $fileReturn->file()->path()
                ->absolute(),
            'checksum' => $fileReturn->checksum(),
        ];

        return $new;
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

    public function toArray(): array
    {
        return $this->array;
    }

    private function getPath(string $name): PathContract
    {
        $this->assertKeyName($name);

        return $this->dir->path()
            ->getChild($name . '.php');
    }

    private function assertKeyName(string $key): void
    {
        if (preg_match_all('#[' . CacheContract::ILLEGAL_KEY_CHARACTERS . ']#', $key, $matches)) {
            $matches = array_unique($matches[0]);
            $forbidden = implode(', ', $matches);
            throw new InvalidArgumentException(
                (new Message('Use of forbidden character(s) %character%'))
                    ->code('%character%', $forbidden)
                    ->toString()
            );
        }
    }
}
