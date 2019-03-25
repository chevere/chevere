<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use Exception;

class Path
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
        $relativePath = Utils\Str::forwardSlashes($relativePath);

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
        $absolutePath = Utils\Str::forwardSlashes($absolutePath);
        $root = ROOT_PATH;
        if ($rootContext) {
            $root .= $rootContext.'/';
        }

        return Utils\Str::replaceFirst($root, null, $absolutePath);
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
            || (
                \strlen($file) > 3 && ctype_alpha($file[0])
                && ':' === $file[1]
                && strspn($file, '/\\', 2, 1)
            )
            || null !== parse_url($file, PHP_URL_SCHEME)
        ;
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
        if ($stream = static::isStream($path)) {
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
        if (Utils\Str::startsWith('/', $path)) {
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
        if (Utils\Str::contains('://', $path) == false) {
            return false;
        }
        $explode = explode('://', $path, 2);
        if (in_array($explode[0], stream_get_wrappers())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Convert array to path.
     *
     * @param array $array ['path', 'from', 'array']
     *
     * @return string path/from/array
     */
    public static function relativeFromArray(array $array): string
    {
        return implode('/', $array);
    }

    /**
     * Convert array to path.
     *
     * @param array $array ['path', 'from', 'array']
     *
     * @return string /path/from/array
     *                FIXME: Windows paths?
     */
    public static function absoluteFromArray(array $array): string
    {
        return '/'.implode('/', $array);
    }

    /**
     * Returns the path from a path identifier.
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
     * @param string $pathIdentifier path identifier (<dirname>:<file>)
     * @param string $rootContext    Root context for the $pathIdentifier. Must be an absolute path.
     *
     * @return string absolute path
     */
    public static function fromHandle(string $pathIdentifier, string $rootContext = null): string
    {
        try {
            Validation::single('$pathIdentifier', $pathIdentifier, function (string $string) {
                return $string != '' && ctype_space($string) == false;
            }, 'Handle value needed, empty or null string provided.');
        } catch (Exception $e) {
            throw new PathException($e);
        }
        $path = static::normalize($pathIdentifier);
        // Do this to apply Path methods only on explicit $rootContext
        if ($rootContext == null) {
            $rootContext = ROOT_PATH.App\PATH;
        } else {
            if (static::isAbsolute($rootContext) == false) {
                throw new CoreException(
                    (new Message('String %a must be an absolute path, %v provided.'))
                        ->code('%a', '$rootContext')
                        ->code('%v', $rootContext)
                );
            }
            $rootContext = static::resolve(static::normalize($rootContext));
        }
        $rootContext = static::tailDir($rootContext);
        // Resolve the target file (if any)
        if (Utils\Str::contains(':', $path)) {
            $explode = explode(':', $path);
            $filename = end($explode);
            if (is_string($filename)) {
                // Last prop doesn't look like a filename
                if (Utils\Str::contains('/', $filename)) {
                    unset($filename);
                } else {
                    // Append .php by default
                    if (pathinfo($filename, PATHINFO_EXTENSION) == null) {
                        $filename .= '.php';
                    }
                    // Unset the last element (file) from $explode
                    array_pop($explode);
                    // Rebuild path
                    $path = join(':', $explode);
                    if ($path != null) {
                        $path = static::tailDir($path);
                    }
                    $path .= $filename;
                }
            }
        } else {
            // If $path does't contains ":", we assume that it is a directory or a explicit filepath
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            // No extension => add trailing slash to path
            if ($extension == false) {
                $path = static::tailDir($path);
            }
        }
        // $path is not an absolute path neither a wrapper or anything like that
        if (static::isAbsolute($path) == false) {
            $path = $rootContext.$path;
        }
        // Resolve . and ..
        $path = static::resolve($path);

        return $path;
    }

    public static function relativeFromHandle(string $handle, string $rootContext = null): ?string
    {
        $path = static::fromHandle(...func_get_args());

        return static::relative($path, $rootContext);
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
        return Utils\Str::rtail($dir, '/');
    }
}
class PathException extends CoreException
{
}
