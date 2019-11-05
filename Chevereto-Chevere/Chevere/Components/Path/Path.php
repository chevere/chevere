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

use Chevere\Components\Message\Message;
use Chevere\Contracts\Path\PathContract;

use function ChevereFn\stringEndsWith;
use function ChevereFn\stringForwardSlashes;
use function ChevereFn\stringReplaceFirst;
use function ChevereFn\stringRightTail;
use function ChevereFn\stringStartsWith;

/**
 * Handles paths from the project's root location (folder containing app, vendor)
 */
final class Path implements PathContract
{
    /** @var string The passed path */
    private $path;

    /** @var string Root context path (absolute) */
    private $root;

    /** @var bool TRUE if the path ends with .php */
    private $isPhp;

    /** @var string Absolute path */
    private $absolute;

    /** @var string Relative path (to project root) */
    private $relative;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $path)
    {
        $this->root = PathContract::ROOT;
        $this->isPhp = stringEndsWith('.php', $path);
        $this->path = $path;
        $this->assertPathFormat();
        $this->handlePaths();
    }

    /**
     * {@inheritdoc}
     */
    public function absolute(): string
    {
        return $this->absolute;
    }

    /**
     * {@inheritdoc}
     */
    public function relative(): string
    {
        return $this->relative;
    }
    
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function exists(): bool
    {
        if ($this->isStream()) {
            return true;
        }
        $this->clearStatCache();

        return stream_resolve_include_path($this->absolute) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDir(): bool
    {
        $this->clearStatCache();

        return is_dir($this->absolute);
    }

    /**
     * {@inheritdoc}
     */
    public function isFile(): bool
    {
        $this->clearStatCache();
        
        return is_file($this->absolute);
    }

    private function getRelative(): string
    {
        $absolutePath = stringForwardSlashes($this->absolute);

        return stringReplaceFirst($this->root, '', $absolutePath);
    }

    private function handlePaths(): void
    {
        if (stringStartsWith('/', $this->path)) {
            $this->assertAbsolutePath();
            $this->absolute = $this->path;
        } else {
            $this->absolute = $this->getAbsolute();
        }
        if (is_dir($this->absolute)) {
            $this->absolute = stringRightTail($this->absolute, '/');
        }
        $this->relative = $this->getRelative();
    }

    private function getAbsolute(): string
    {
        return $this->root . stringForwardSlashes($this->path);
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

    private function clearStatCache(): void
    {
        clearstatcache(true, $this->absolute);
    }
}
