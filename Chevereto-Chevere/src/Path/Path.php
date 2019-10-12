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

namespace Chevere\Path;

use const Chevere\ROOT_PATH;

use Chevere\Message\Message;
use InvalidArgumentException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use RuntimeException;

use function ChevereFn\stringForwardSlashes;
use function ChevereFn\stringReplaceFirst;

final class Path
{
    /** @var string The passed path */
    private $path;

    /** @var string Absolute path */
    private $absolute;

    /** @var string Relative path */
    private $relative;

    /** @var string Context path (absolute) */
    private $context;

    public function __construct(string $path)
    {
        // Resolve . and ..
        // $this->path = pathResolve($this->path);
        $this->context = ROOT_PATH;
        $this->path = $path;
        if (preg_match('#[\.\/]#', $this->path)) {
            $this->resolve();
        }
        if ($this->isAbsolute()) {
            $this->absolute = $path;
            $this->relative = $this->getRelative();
        } else {
            $this->absolute = $this->getAbsolute();
            $this->relative = $path;
        }
    }

    public function withContext(string $context): Path
    {
        $path = new static($context);
        $new = clone $this;
        $new->context = $path->absolute();
        $new->absolute = $new->getAbsolute();
        $new->relative = $new->getRelative();

        return $new;
    }

    /**
     * Return the path (absolute)
     */
    public function absolute(): string
    {
        return $this->absolute;
    }

    /**
     * Return the path (relative)
     */
    public function relative(): string
    {
        return $this->relative;
    }

    /**
     * Detects if the path is a stream URL.
     */
    public function isStream(): bool
    {
        if (false === strpos($this->absolute, '://')) {
            return false;
        }
        $explode = explode('://', $this->absolute, 2);

        return in_array($explode[0], stream_get_wrappers());
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @see https://github.com/symfony/symfony/blob/4.2/src/Symfony/Component/Filesystem/Filesystem.php
     *
     * @return bool
     */
    private function isAbsolute(): bool
    {
        return strspn($this->path, '/\\', 0, 1)
            || (\strlen($this->path) > 3 && ctype_alpha($this->path[0])
                && ':' === $this->path[1]
                && strspn($this->path, '/\\', 2, 1))
            || null !== parse_url($this->path, PHP_URL_SCHEME);
    }

    /**
     * Resolve the given path (dots).
     *
     * Taken from https://stackoverflow.com/a/53598213/1145912
     */
    private function resolve(): void
    {
        $num = 0;
        $aux = preg_replace("/\/\.\//", '/', $this->path);
        $parts = $aux == null ? [] : explode('/', $aux);
        $partsReverse = [];
        for ($i = count($parts) - 1; $i >= 0; --$i) {
            if (trim($parts[$i]) === '..') {
                ++$num;
            } else {
                if ($num > 0) {
                    --$num;
                    continue;
                }
                $partsReverse[] = $parts[$i];
            }
        }
        $this->absolute = implode('/', array_reverse($partsReverse));
    }

    /**
     * Converts relative path to absolute path.
     *
     * @return string absolute path
     */
    private function getAbsolute(): string
    {
        return $this->context . stringForwardSlashes($this->path);
    }

    /**
     * Converts absolute path to relative path.
     *
     * @param string $absolutePath an absolute path in the system
     *
     * @return string relative path (relative to html root)
     */
    private function getRelative(): string
    {
        $absolutePath = stringForwardSlashes($this->absolute);

        return stringReplaceFirst($this->context, '', $absolutePath);
    }

    /**
     * Creates a path
     *
     * @return string The created path (absolute)
     */
    public function create(): void
    {
        if (!mkdir($this->absolute, 0777, true)) {
            throw new RuntimeException(
                (new Message('Unable to create path %path%'))
                    ->code('%path%', $this->path)
            );
        }
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
    // private function pathNormalize(string $path): string
    // {
    //     $wrapper = '';
    //     $stream = (new Path($path))->isStream();
    //     if ($stream) {
    //         [$wrapper, $path] = $stream;
    //         $wrapper .= '://';
    //     }
    //     // Standardise all paths to use /
    //     $path = str_replace('\\', '/', $path ?? '');
    //     // Replace multiple slashes down to a singular, allowing for network shares having two slashes.
    //     $path = preg_replace('|(?<=.)/+|', '/', $path);
    //     if ($path == null) {
    //         return '';
    //     }
    //     // Chevereto: Get rid of any extra slashes at the begining if needed
    //     if (stringStartsWith('/', $path)) {
    //         $path = '/' . ltrim($path, '/');
    //     }
    //     // Windows paths should uppercase the drive letter
    //     if (':' === substr($path, 1, 1)) {
    //         $path = ucfirst($path);
    //     }

    //     return rtrim($wrapper . $path, '/');
    // }

    /**
     * Removes the contents from a path, without deleting the path.
     *
     * @return array List of deleted contents.
     */
    public function removeContents(): array
    {
        $this->assertDir();
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->absolute, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        $removed = [];
        foreach ($files as $fileinfo) {
            $content = $fileinfo->getRealPath();
            if ($fileinfo->isDir()) {
                rmdir($content);
                $removed[] = $content;
                continue;
            }
            unlink($content);
            $removed[] = $content;
        }

        return $removed;
    }

    public function isDir(): bool
    {
        return is_dir($this->absolute);
    }

    private function assertDir(): void
    {
        if (!$this->isDir()) {
            throw new InvalidArgumentException(
                (new Message('%path% is not a directory'))
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }
}
