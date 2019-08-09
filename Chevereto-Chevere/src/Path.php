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

use Chevere\Utility\Str;

abstract class Path
{
    /**
     * Converts relative path to absolute path.
     *
     * @param string $relativePath a relative path (relative to html root)
     *
     * @return string absolute path
     */
    public static function absolute(string $relativePath): string
    {
        $relativePath = Utility\Str::forwardSlashes($relativePath);

        return ROOT_PATH.$relativePath;
    }

    /**
     * Converts absolute path to relative path.
     *
     * @param string $absolutePath an absolute path in the system
     * @param string $rootContext  root context directory
     *
     * @return string relative path (relative to html root)
     */
    public static function relative(string $absolutePath, string $rootContext = null): ?string
    {
        $absolutePath = Str::forwardSlashes($absolutePath);
        $root = ROOT_PATH;
        if ($rootContext) {
            $root .= $rootContext.'/';
        }

        return Str::replaceFirst($root, null, $absolutePath);
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @see https://github.com/symfony/symfony/blob/4.2/src/Symfony/Component/Filesystem/Filesystem.php
     *
     * @param string $file A file path
     *
     * @return bool
     */
    public static function isAbsolute($file): bool
    {
        return strspn($file, '/\\', 0, 1)
            || (\strlen($file) > 3 && ctype_alpha($file[0])
                && ':' === $file[1]
                && strspn($file, '/\\', 2, 1))
            || null !== parse_url($file, PHP_URL_SCHEME);
    }

    /**
     * Normalize a filesystem path.
     *
     * On windows systems, replaces backslashes with forward slashes
     * and forces upper-case drive letters.
     * Allows for two leading slashes for Windows network shares, but
     * ensures that all other duplicate slashes are reduced to a single.
     *
     * Forked from WordPress.
     *
     * @param string $path path to normalize
     *
     * @return string normalized path, without trailing slash
     */
    public static function normalize(string $path): string
    {
        $wrapper = '';
        $stream = static::isStream($path);
        if ($stream) {
            [$wrapper, $path] = $stream;
            $wrapper .= '://';
        }
        // Standardise all paths to use /
        $path = str_replace('\\', '/', $path ?? '');
        // Replace multiple slashes down to a singular, allowing for network shares having two slashes.
        $path = preg_replace('|(?<=.)/+|', '/', $path);
        if ($path == null || is_array($path)) {
            return '';
        }
        // Chevereto: Get rid of any extra slashes at the begining if needed
        if (Str::startsWith('/', $path)) {
            $path = '/'.ltrim($path, '/');
        }
        // Windows paths should uppercase the drive letter
        if (':' === substr($path, 1, 1)) {
            $path = ucfirst($path);
        }

        return rtrim($wrapper.$path, '/');
    }

    /**
     * Resolve a given path (dots).
     *
     * Taken from https://stackoverflow.com/a/53598213/1145912
     *
     * @param string $path Path to resolve
     *
     * @return string Resolved path
     */
    public static function resolve(string $path): string
    {
        $n = 0;
        $aux = preg_replace("/\/\.\//", '/', $path);
        $parts = $aux == null ? [] : explode('/', $aux);
        $partsReverse = [];
        for ($i = count($parts) - 1; $i >= 0; --$i) {
            if (trim($parts[$i]) === '..') {
                ++$n;
            } else {
                if ($n > 0) {
                    --$n;
                } else {
                    $partsReverse[] = $parts[$i];
                }
            }
        }

        return implode('/', array_reverse($partsReverse));
    }

    /**
     * Test if a given path is a stream URL.
     *
     * @param string $path the resource path or URL
     */
    public static function isStream(string $path): bool
    {
        if (!Str::contains('://', $path)) {
            return false;
        }
        $explode = explode('://', $path, 2);

        return in_array($explode[0], stream_get_wrappers());
    }

    /**
     * Returns the path associated with a path identifier relative to app.
     *
     * Path identifiers refers to the standarized way in which files and paths
     * are handled by internal APIs like Hookable or Router.
     *
     * A path identifier looks like this:
     * dirname:file
     *
     * - The dirname is relative to App\Path.
     * - dirname allows absolute paths.
     *
     * @param string $appPathIdentifier path identifier relative to app (<dirname>:<file>)
     *
     * @return string absolute path like /home/user/app/ or /home/user/app/file.php
     */
    public static function fromIdentifier(string $appPathIdentifier): string
    {
        $pathHandle = static::handle(...func_get_args());

        return $pathHandle->path();
    }

    /**
     * Returns a PathHandle for the given path identifier relative to app.
     *
     * @param string $appPathIdentifier path identifier relative to app (<dirname>:<file>)
     */
    public static function handle(string $appPathIdentifier): PathHandle
    {
        $rootContext = null;
        // $handleContext = static::resolve(static::normalize($rootContext));
        $handleContext = static::tailDir(ROOT_PATH.App\PATH);
        $pathHandle = new PathHandle($appPathIdentifier, $handleContext);

        return $pathHandle;
    }

    /**
     * Adds a trailing slash for a given string.
     *
     * @param string $dir directory to tail
     *
     * @return string tailed directory (slash)
     */
    public static function tailDir(string $dir): string
    {
        return Str::rtail($dir, '/');
    }
}
