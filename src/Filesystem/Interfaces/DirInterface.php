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

namespace Chevere\Filesystem\Interfaces;

use Chevere\Filesystem\Exceptions\DirNotExistsException;
use Chevere\Filesystem\Exceptions\DirUnableToCreateException;
use Chevere\Filesystem\Exceptions\DirUnableToRemoveException;
use Chevere\Filesystem\Exceptions\FileUnableToRemoveException;
use Chevere\Filesystem\Exceptions\PathInvalidException;
use Chevere\Filesystem\Exceptions\PathIsFileException;
use Chevere\Filesystem\Exceptions\PathTailException;

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
     * Creates the directory if it doesn't exists.
     *
     * @param int $mode Octal mask
     *
     * @throws DirUnableToCreateException
     */
    public function createIfNotExists(int $mode = 0755): void;

    /**
     * Returns a boolean indicating whether the directory exists.
     */
    public function exists(): bool;

    /**
     * @throws DirNotExistsException
     */
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
     * Removes the directory and its contents.
     *
     * @return array An array with all the elements removed
     *
     * @throws DirUnableToRemoveException if unable to remove the directory
     */
    public function remove(): array;

    /**
     * Same as remove, but only if the directory exists.
     *
     * @throws DirUnableToRemoveException if unable to remove the directory
     */
    public function removeIfExists(): array;

    /**
     * Gets a child `DirInterface` for the added path.
     *
     * @throws PathInvalidException
     */
    public function getChild(string $path): self;
}
