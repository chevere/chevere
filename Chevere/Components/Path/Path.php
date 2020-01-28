<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Path;

use Chevere\Components\Message\Message;
use Chevere\Components\Path\Exceptions\PathInvalidException;
use Chevere\Components\Path\Interfaces\PathInterface;
use function ChevereFn\stringStartsWith;

/**
 * Handles paths with context.
 */
class Path implements PathInterface
{
    /** @var string Absolute path */
    private string $absolute;

    /**
     * Creates a new instance.
     *
     * @param string $absolute An absolute filesystem path
     */
    public function __construct(string $absolute)
    {
        new CheckFormat($absolute);
        $this->absolute = $absolute;
        $this->assertAbsolutePath();
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
    // public function isStream(): bool
    // {
    //     if (false === strpos($this->absolute, '://')) {
    //         return false;
    //     }
    //     $explode = explode('://', $this->absolute, 2);

    //     return in_array($explode[0], stream_get_wrappers());
    // }

    /**
     * {@inheritdoc}
     */
    public function exists(): bool
    {
        $this->clearStatCache();

        return false !== stream_resolve_include_path($this->absolute);
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

    /**
     * {@inheritdoc}
     */
    public function getChild(string $path): PathInterface
    {
        $parent = $this->absolute();
        $childrenPath = rtrim($parent, '/');

        return new Path($childrenPath . '/' . $path);
    }

    public function assertAbsolutePath(): void
    {
        if (!stringStartsWith('/', $this->absolute)) {
            throw new PathInvalidException(
                (new Message('Only absolute paths can be used to construct a %className% instance'))
                    ->code('%className%', __CLASS__)
                    ->toString()
            );
        }
    }

    private function clearStatCache(): void
    {
        clearstatcache(true, $this->absolute);
    }
}
