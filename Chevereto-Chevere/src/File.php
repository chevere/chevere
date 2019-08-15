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

    /*
     * Get file path identifier.
     *
     * Path identifiers are always relative to App\PATH.
     *
     * @param string $file file path, if null it will detect file caller
     */
    // public static function identifier(?string $file): string
    // {
    //     if (!isset($file)) {
    //         $file = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'];
    //     }
    //     $pathinfo = pathinfo($file);
    //     $dirname = $pathinfo['dirname'];
    //     // Relativize to App\PATH
    //     $dirname = Utility\Str::replaceFirst(App\PATH, null, $dirname);
    //     if ($dirname == rtrim(App\PATH, '/')) { // Means that $file is at App\PATH
    //         $dirname = null;
    //     }

    //     return $dirname.':'.$pathinfo['filename'];
    // }
}
