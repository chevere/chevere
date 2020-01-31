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

namespace Chevere\Components\Filesystem\Path\Interfaces;

use Chevere\Components\Filesystem\Path\Exceptions\PathNotAbsoluteException;
use Chevere\Components\Filesystem\Path\Exceptions\PathDoubleDotsDashException;
use Chevere\Components\Filesystem\Path\Exceptions\PathDotSlashException;
use Chevere\Components\Filesystem\Path\Exceptions\PathExtraSlashesException;

interface PathInterface
{
    /**
     * Creates a new instance.
     *
     * @param string $absolute An absolute filesystem path
     * @throws PathNotAbsoluteException
     * @throws PathDoubleDotsDashException
     * @throws PathDotSlashException
     * @throws PathExtraSlashesException
     */
    public function __construct(string $absolute);

    /**
     * Return absolute path
     */
    public function absolute(): string;

    // /**
    //  * Returns a boolean indicating whether the path is a stream.
    //  */
    // public function isStream(): bool;

    /**
     * Returns a boolean indicating whether the path exists.
     */
    public function exists(): bool;

    /**
     * Returns a boolean indicating whether the path is a directory and exists.
     */
    public function isDir(): bool;

    /**
     * Returns a boolean indicating whether the path is a file and exists.
     */
    public function isFile(): bool;

    /**
     * Wrapper for \chmod.
     * @throws PathIsNotDirectoryException
     * @throws PathUnableToChmodException
     */
    public function chmod(int $mode): void;

    /**
     * Wrapper for \is_writeable.
     * @throws PathIsNotDirectoryException
     */
    public function isWriteable(): bool;

    /**
     * Wrapper for \is_writeable.
     * @throws PathIsNotDirectoryException
     */
    public function isReadable(): bool;

    /**
     * Get a child path as a PathInterface
     */
    public function getChild(string $path): PathInterface;
}
