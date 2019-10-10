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

namespace ChevereFn;

use LogicException;
use RuntimeException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Chevere\Message\Message;
use InvalidArgumentException;

use const Chevere\ROOT_PATH;

/**
 * Returns whether the file path is an absolute path.
 *
 * @see https://github.com/symfony/symfony/blob/4.2/src/Symfony/Component/Filesystem/Filesystem.php
 *
 * @param string $path A file path
 *
 * @return bool
 */
function pathIsAbsolute(string $path): bool
{
    return strspn($path, '/\\', 0, 1)
        || (\strlen($path) > 3 && ctype_alpha($path[0])
            && ':' === $path[1]
            && strspn($path, '/\\', 2, 1))
        || null !== parse_url($path, PHP_URL_SCHEME);
}

/**
 * Test if a given path is a stream URL.
 *
 * @param string $path the resource path or URL
 */
function pathIsStream(string $path): bool
{
    if (false === strpos($path, '://')) {
        return false;
    }
    $explode = explode('://', $path, 2);

    return in_array($explode[0], stream_get_wrappers());
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
function pathResolve(string $path): string
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
 * Normalize a filesystem path.
 *
 * On windows systems, replaces backslashes with forward slashes
 * and forces upper-case drive letters.
 * Allows for two leading slashes for Windows network shares, but
 * ensures that all other duplicate slashes are reduced to a single.
 *
 * Stolen from WordPress.
 *
 * @param string $path path to normalize
 *
 * @return string normalized path, without trailing slash
 */
function pathNormalize(string $path): string
{
    $wrapper = '';
    $stream = pathIsStream($path);
    if ($stream) {
        [$wrapper, $path] = $stream;
        $wrapper .= '://';
    }
    // Standardise all paths to use /
    $path = str_replace('\\', '/', $path ?? '');
    // Replace multiple slashes down to a singular, allowing for network shares having two slashes.
    $path = preg_replace('|(?<=.)/+|', '/', $path);
    if ($path == null) {
        return '';
    }
    // Chevereto: Get rid of any extra slashes at the begining if needed
    if (stringStartsWith('/', $path)) {
        $path = '/' . ltrim($path, '/');
    }
    // Windows paths should uppercase the drive letter
    if (':' === substr($path, 1, 1)) {
        $path = ucfirst($path);
    }

    return rtrim($wrapper . $path, '/');
}

/**
 * Converts relative path to absolute path.
 *
 * @param string $relativePath a relative path (relative to html root)
 *
 * @return string absolute path
 */
function pathAbsolute(string $relativePath): string
{
    $relativePath = stringForwardSlashes($relativePath);

    return ROOT_PATH . $relativePath;
}

/**
 * Converts absolute path to relative path.
 *
 * @param string $absolutePath an absolute path in the system
 * @param string $rootContext  root context directory
 *
 * @return string relative path (relative to html root)
 */
function pathRelative(string $absolutePath, string $rootContext = null): ?string
{
    $absolutePath = stringForwardSlashes($absolutePath);
    $root = ROOT_PATH;
    if ($rootContext) {
        $root .= $rootContext . '/';
    }

    return stringReplaceFirst($root, '', $absolutePath);
}

/**
 * Creates a path
 *
 * @return string The created path (absolute)
 */
function pathCreate(string $path): string
{
    if (!mkdir($path, 0777, true)) {
        throw new RuntimeException(
            (new Message('Unable to create path %path%'))
                ->code('%path%', $path)
        );
    }

    return $path;
}

/**
 * Removes the contents from a path, without deleting the path.
 *
 * @return array List of deleted contents.
 */
function pathRemoveContents(string $path): array
{
    if (!is_dir($path)) {
        throw new InvalidArgumentException(
            (new Message('%path% is not a directory'))
                ->code('%path%', $path)
                ->toString()
        );
    }
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $removed = [];
    foreach ($files as $fileinfo) {
        $content = $fileinfo->getRealPath();
        if ($fileinfo->isDir()) {
            rmdir($content);
        } else {
            unlink($content);
        }
        $removed[] = $content;
    }

    return $removed;
}
