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

namespace Chevere\Interfaces\Filesystem;

interface PathInterface
{
    /**
     * Return absolute path
     */
    public function absolute(): string;

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
    public function isWritable(): bool;

    /**
     * Wrapper for \is_writeable.
     * @throws PathIsNotDirectoryException
     */
    public function isReadable(): bool;

    /**
     * Get a child path as a PathInterface
     */
    public function getChild(string $child): PathInterface;
}
