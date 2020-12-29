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

use Chevere\Exceptions\Filesystem\DirUnableToCreateException;
use Chevere\Exceptions\Filesystem\DirUnableToRemoveException;
use Chevere\Exceptions\Filesystem\FileUnableToRemoveException;
use Chevere\Exceptions\Filesystem\PathInvalidException;
use Chevere\Exceptions\Filesystem\PathIsFileException;
use Chevere\Exceptions\Filesystem\PathTailException;

/**
 * Describes the component in charge of interacting with filesystem directories.
 */
interface DirInterface
{
    /**
     * @throws PathIsFileException
     * @throws PathTailException
     */
    public function __construct(PathInterface $path);

    /**
     * Provides access to the PathInterface instance.
     */
    public function path(): PathInterface;

    /**
     * Creates the directory.
     *
     * @param int $mode Octal mask
     *
     * @throws DirUnableToCreateException
     */
    public function create(int $mode = 0755): void;

    /**
     * Returns a boolean indicating whether the directory exists.
     */
    public function exists(): bool;

    public function assertExists(): void;

    /**
     * Removes the contents from a path without deleting the path.
     *
     * @return array an array with all the dir contents removed
     *
     * @throws DirUnableToRemoveException if unable to remove the directory
     * @throws FileUnableToRemoveException if unable to remove a file in the directory
     */
    public function removeContents(): array;

    /**
     * Removes the directory.
     *
     * @return array An array with all the elements removed
     *
     * @throws DirUnableToRemoveException if unable to remove the directory
     */
    public function remove(): array;

    /**
     * Gets a child `DirInterface` for the added path.
     *
     * @throws PathInvalidException
     */
    public function getChild(string $path): self;
}
