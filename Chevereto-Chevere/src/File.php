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

namespace Chevere;

use RuntimeException;
use Chevere\Path\Path;

final class File
{
    /**
     * Fast wat to determine if a file or directory exists using stream_resolve_include_path.
     *
     * @param string $filename   Absolute file path
     * @param bool   $clearCache TRUE to call clearstatcache
     *
     * @return bool TRUE if the $filename exists
     */
    public static function exists(string $filename, bool $clearCache = true): bool
    {
        if ($clearCache) {
            clearstatcache(true);
        }
        // Only tweak relative paths, without wrappers or anything else
        // Note that stream_resolve_include_path won't work with relative paths if no chdir().
        if (!Path::isAbsolute($filename)) {
            $filename = Path::absolute($filename);
        }

        return stream_resolve_include_path($filename) !== false;
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
}
