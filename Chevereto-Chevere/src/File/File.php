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

namespace Chevere\File;

use RuntimeException;
use Chevere\Message\Message;
use Chevere\Path\Path;

final class File
{
    /** @var string Absolute filename */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
        if (!Path::isAbsolute($path)) {
            $this->path = Path::absolute($path);
        }
    }

    /**
     * Fast wat to determine if a file or directory exists using stream_resolve_include_path.
     *
     * @return bool TRUE if the $filename exists
     */
    // FIXME: This should be moved to Path
    public function exists(): bool
    {
        $this->clearStatCache();

        return stream_resolve_include_path($this->path) !== false;
    }

    public static function put(string $filename, $contents): void
    {
        if (!static::exists($filename)) {
            $dirname = dirname($filename);
            if (!static::exists($dirname)) {
                Path::create($dirname);
            }
        }
        if (false === @file_put_contents($filename, $contents)) {
            throw new RuntimeException(
                (new Message('Unable to write content to file %filepath%'))
                    ->code('%filepath%', $filename)
                    ->toString()
            );
        }
    }

    private function clearStatCache(): void
    {
        clearstatcache(true, $this->path);
    }
}
