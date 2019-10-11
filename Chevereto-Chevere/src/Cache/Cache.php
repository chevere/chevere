<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Cache;

use InvalidArgumentException;
use Chevere\Message\Message;
use Chevere\FileReturn\FileReturn;
use Chevere\Path\PathHandle;

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

    /** @var string Base key used to generate a file identifier as cache-key system */
    private $baseKey;

    /** @var string Chache name (user input) */
    private $name;

    /** @var array An array [key => [checksum => , path =>]] containing information about the cache instance */
    private $array;

    public function __construct(string $name)
    {
        $this->validateKey($name);
        $this->name = $name;
        $this->baseKey = 'cache/' . $name . ':';
    }

    /**
     * Get cache as a FileReturn object
     *
     * @return FileReturn A FileReturn instance for the cache file.
     */
    public function get(string $key): FileReturn
    {
        $identifier = $this->getFileIdentifier($key);
        return new FileReturn(
            (new PathHandle($identifier))->path()
        );
    }

    public function exists(string $key): bool
    {
        $file = (new PathHandle($this->getFileIdentifier($key)))->file();

        return $file->exists();
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
            'path' => $fileReturn->path(),
            'checksum' => $fileReturn->checksum(),
        ];
        return $fileReturn;
    }

    public function remove(string $key): void
    {
        $pathHandle = new PathHandle($this->getFileIdentifier($key));
        if (!$pathHandle->file()->exists()) {
            return;
        }
        $pathHandle->file()->remove();
        unset($this->array[$this->name][$key]);
    }

    public function toArray(): array
    {
        return $this->array;
    }

    /**
     * @return string Cache file path identifier for the given $name
     */
    private function getFileIdentifier(string $name): string
    {
        $this->validateKey($name);
        return $this->baseKey . $name;
    }

    private function validateKey(string $key): void
    {
        if (preg_match_all('#[' . static::ILLEGAL_KEY_CHARACTERS . ']#', $key, $matches)) {
            $matches = array_unique($matches[0]);
            $forbidden = implode(', ', $matches);
            throw new InvalidArgumentException(
                (new Message('Use of forbidden character %forbidden%.'))
                    ->code('%forbidden%', $forbidden)
                    ->toString()
            );
        }
    }
}
