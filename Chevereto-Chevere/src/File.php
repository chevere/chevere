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

class File
{
    /**
     * @TODO: use ralouphie/mimey https://packagist.org/packages/ralouphie/mimey
     */
    const MIMETYPES = [
        'image/x-windows-bmp' => 'bmp',
        'image/x-ms-bmp' => 'bmp',
        'image/bmp' => 'bmp',
        'image/gif' => 'gif',
        'image/pjpeg' => 'jpg',
        'image/jpeg' => 'jpg',
        'image/x-png' => 'png',
        'image/png' => 'png',
        'image/x-tiff' => 'tiff',
        'image/tiff' => 'tiff',
        'image/x-icon' => 'ico',
        'image/x-rgb' => 'rgb',
    ];

    /**
     * Gets the mimetype of a file.
     *
     * @param string $filename file to read
     *
     * @return string mimetipe
     */
    public static function mimetype(string $filename): ?string
    {
        if (defined('FILEINFO_MIME_TYPE') && function_exists('finfo_open')) {
            return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filename);
        }
        if (function_exists('mime_content_type')) {
            return (string) mime_content_type($filename);
        }

        return null;
    }

    /**
     * Gets the corresponding file extension from a known mimetype string.
     *
     * @param string $mimetype file mimetype string
     *
     * @return string file extension
     */
    public static function extensionFromMime(string $mimetype): ?string
    {
        return static::MIMETYPES[$mimetype];
    }

    /**
     * Gets a file extension based on the pathinfo() function.
     *
     * @param string $filename file path you wish to retrieve its extension
     *
     * @return string file extension
     */
    public static function extension(string $filename): ?string
    {
        $mime = static::mimetype($filename);
        if (isset($mime)) {
            return static::extensionFromMime($mime);
        }

        return null;
    }

    /**
     * Gets the name of a file from its file path.
     *
     * @param string $filename file you wish to retrieve its name
     *
     * @return string file name
     */
    public static function name(string $filename): string
    {
        return pathinfo($filename, PATHINFO_FILENAME);
    }

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

    /**
     * Retrieves information about a file.
     *
     * @param string $filename file
     *
     * @return array file info array
     */
    public static function info(string $filename): array
    {
        $filesize = filesize($filename);
        if (!$filesize) {
            return [];
        }
        $mime = static::mimetype($filename);
        $basename = basename($filename); // file.ext
        $name = Utility\Str::replaceLast('.' . File::extension($filename), null, $basename); // file
        $info = [
            'basename' => $basename,
            'name' => $name,
            'extension' => isset($mime) ? File::extensionFromMime($mime) : null,
            'size' => intval($filesize),
            'size_format' => Utility\Bytes::format((string) $filesize),
            'mime' => $mime,
            // 'url'		=> absolutePathToUrl($filename),
            'md5' => md5_file($filename),
        ];

        return $info;
    }

    /**
     * Get file path identifier.
     *
     * Path identifiers are always relative to App\PATH.
     *
     * @param string $file file path, if null it will detect file caller
     */
    public static function identifier(?string $file): string
    {
        if (!isset($file)) {
            $file = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'];
        }
        $pathinfo = pathinfo($file);
        $dirname = $pathinfo['dirname'];
        // Relativize to App\PATH
        $dirname = Utility\Str::replaceFirst(App\PATH, null, $dirname);
        if ($dirname == rtrim(App\PATH, '/')) { // Means that $file is at App\PATH
            $dirname = null;
        }

        return $dirname . ':' . $pathinfo['filename'];
    }
}
