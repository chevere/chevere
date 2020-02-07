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

namespace Chevere\Components\Filesystem\Interfaces\Dir;

use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use Chevere\Components\Filesystem\Exceptions\Dir\DirUnableToRemoveException;
use Chevere\Components\Filesystem\Exceptions\Dir\DirUnableToCreateException;
use Chevere\Components\Filesystem\Exceptions\File\FileUnableToRemoveException;

interface DirInterface
{
    public function __construct(PathInterface $path);

    /**
     * Provides access to the PathInterface instance.
     */
    public function path(): PathInterface;

    /**
     * Returns a boolean indicating whether the directory exists.
     */
    public function exists(): bool;

    /**
     * Creates the directory.
     *
     * @param int $mode Octal mask
     *
     * @throws DirUnableToCreateException if unable to create the directoy
     */
    public function create(int $mode = 0777): void;

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
     * Wrapper for \rmdir.
     *
     * @throws DirUnableToRemoveException if rmdir failure
     */
    public function rmdir(): void;

    /**
     * Gets a child DirInterface for the added path.
     */
    public function getChild(string $path): DirInterface;
}
