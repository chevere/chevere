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

namespace Chevere\Components\Path;

use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

use Chevere\Components\Message\Message;
use Chevere\Components\Stopwatch\Stopwatch;

use function ChevereFn\stringEndsWith;
use function ChevereFn\stringForwardSlashes;
use function ChevereFn\stringReplaceFirst;
use function ChevereFn\stringReplaceLast;
use function ChevereFn\stringRightTail;
use function ChevereFn\stringStartsWith;

use const Chevere\APP_PATH;
use const Chevere\ROOT_PATH;

/**
 * Handles paths from the project's root location (folder containing app, vendor)
 */
final class Path
{
    /** @var string The passed path */
    private $path;

    /** @var string Root context path (absolute) */
    private $root;

    /** @var bool TRUE if the path ends with .php */
    private $isPHP;

    /** @var string Absolute path */
    private $absolute;

    /** @var string Relative path (to project root) */
    private $relative;

    public function __construct(string $path)
    {
        $this->root = APP_PATH;
        $this->isPHP = stringEndsWith('.php', $path);
        $this->path = $path;
        $this->assertPathFormat();
        $this->handlePaths();
    }

    private function handlePaths(): void
    {
        if (stringStartsWith('/', $this->path)) {
            $this->assertAbsolutePath();
            $this->absolute = $this->path;
        } else {
            $this->absolute = $this->getAbsolute();
        }
        $this->setConditions();
        $this->relative = $this->getRelative();
    }

    private function setConditions(): void
    {
        $this->isDir = false;
        $this->isFile = false;
        if (is_dir($this->absolute)) {
            $this->isFile = false;
            $this->isDir = true;
            $this->absolute = stringRightTail($this->absolute, '/');
        } elseif (is_file($this->absolute)) {
            $this->isFile = true;
            $this->isDir = false;
        }
    }

    public function identifier(): string
    {
        return $this->identifier;
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
     * Converts relative path to absolute path.
     *
     * @return string absolute path
     */
    private function getAbsolute(): string
    {
        return $this->root . stringForwardSlashes($this->path);
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

        return stringReplaceFirst($this->root, '', $absolutePath);
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

    private function assertAbsolutePath(): void
    {
        if (!stringStartsWith($this->root, $this->path)) {
            throw new InvalidArgumentException(
                (new Message('Only absolute paths in the app path %root% are allowed, path %path% provided'))
                    ->code('%root%', $this->root)
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }

    private function assertPathFormat(): void
    {
        if (false !== strpos($this->path, '//')) {
            throw new InvalidArgumentException(
                (new Message('Path %path% contains extra-slashes'))
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
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
