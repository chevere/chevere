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

use Chevere\Components\File\File;
use Chevere\Components\FileReturn\FileReturn;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;

use function ChevereFn\stringRightTail;

/**
 * A simple PHP based cache system.
 *
 * Using FileReturn, it provides cache by using php files that return a single variable.
 *
 * cached.php >>> <?php return 'my cached data';
 *
 */
final class Cache
{
    const ILLEGAL_KEY_CHARACTERS = '\.\/\\\~\:';

    /** @var string Cache name */
    private $name;

    /** @var string Absolute path to working folder (taken from $path) */
    private $workingFolder;

    /** @var array An array [key => [checksum => , path =>]] containing information about the cache instance */
    private $array;

    /**
     * @param string $name Named cache entry (folder)
     * @param Path $path The working path where cache files will be stored/accesed
     */
    public function __construct(string $name, Path $path)
    {
        $this->assertKeyName($name);
        $this->name = $name;
        if (!$path->isDir()) {
            throw new InvalidArgumentException(
                (new Message("Path %path% is not a directory"))
                    ->code('%path%', $path->absolute())
                    ->toString()
            );
        }
        $this->workingFolder = stringRightTail($path->absolute(), '/') . $name . '/';
    }

    /**
     * Get cache as a FileReturn object
     *
     * @return FileReturn A FileReturn instance for the cache file.
     */
    public function get(string $key): FileReturn
    {
        return new FileReturn($this->getPath($key));
    }

    public function exists(string $key): bool
    {
        return $this->getPath($key)->exists();
    }

    /**
     * Put cache
     *
     * @param string $key Cache key
     * @param mixed $var Anything, but keep it restricted to one-dimension iterables at most.
     *
     * @return FileReturn A FileReturn instance for the cached file.
     */
    public function put(string $key, $var): FileReturn
    {
        $fileReturn = $this->get($key);
        $fileReturn->put($var);
        $this->array[$this->name][$key] = [
            'path' => $fileReturn->path()->absolute(),
            'checksum' => $fileReturn->checksum(),
        ];
        return $fileReturn;
    }

    public function remove(string $key): void
    {
        $path = $this->getPath($key);
        if (!$path->exists()) {
            return;
        }
        (new File($path))->remove();
        unset($this->array[$this->name][$key]);
    }

    public function toArray(): array
    {
        return $this->array;
    }

    private function getPath(string $name): Path
    {
        $this->assertKeyName($name);
        return new Path($this->workingFolder . $name . '.php');
    }

    private function assertKeyName(string $key): void
    {
        if (preg_match_all('#[' . static::ILLEGAL_KEY_CHARACTERS . ']#', $key, $matches)) {
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
