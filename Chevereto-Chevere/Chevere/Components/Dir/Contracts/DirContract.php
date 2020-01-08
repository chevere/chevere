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

namespace Chevere\Components\Dir\Contracts;

use Chevere\Contracts\Path\PathContract;
use Chevere\Components\Dir\Exceptions\DirUnableToRemoveException;
use Chevere\Components\Dir\Exceptions\DirUnableToCreateException;
use Chevere\Components\File\Exceptions\FileUnableToRemoveException;
use Chevere\Components\Path\Exceptions\PathIsFileException;

interface DirContract
{
    /**
     * Creates a new instance.
     *
     * @throws PathIsFileException if the PathContract represents a file
     */
    public function __construct(PathContract $path);

    /**
     * Provides access to the PathContract instance.
     */
    public function path(): PathContract;

    /**
     * Returns a boolean indicating whether the directory exists.
     */
    public function exists(): bool;

    /**
     * Creates the directory.
     *
     * @throws DirExistsException         if the directory already exists
     * @throws DirUnableToCreateException if unable to create the directoy
     */
    public function create(): void;

    /**
     * Removes the directory.
     *
     * @return array An array with all the elements removed
     *
     * @throws DirUnableToRemoveException if unable to remove the directory
     */
    public function remove(): array;

    /**
     * Removes the contents from a path without deleting the path.
     *
     * @return array an array with all the dir contents removed
     *
     * @throws DirUnableToRemoveException  if unable to remove the directory
     * @throws FileUnableToRemoveException if unable to remove a file in the directory
     */
    public function removeContents(): array;

    /**
     * Gets a child DirContract for the added path.
     */
    public function getChild(string $path): DirContract;
}
