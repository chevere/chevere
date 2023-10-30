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

/**
 * Describes the component in charge of interacting with filesystem directory.
 */
interface DirectoryInterface
{
    /**
     * Provides access to the PathInterface instance.
     */
    public function path(): PathInterface;

    /**
     * Creates the directory.
     *
     * @param int $mode Octal mask
     */
    public function create(int $mode = 0755): void;

    /**
     * Creates the directory if it doesn't exists.
     *
     * @param int $mode Octal mask
     */
    public function createIfNotExists(int $mode = 0755): void;

    /**
     * Indicates whether the directory exists.
     */
    public function exists(): bool;

    public function assertExists(): void;

    /**
     * Removes the contents from a path without deleting the path.
     *
     * @return string[] dir contents removed
     */
    public function removeContents(): array;

    /**
     * Removes the directory and its contents.
     *
     * @return string[] elements removed
     */
    public function remove(): array;

    /**
     * Same as remove, but only if the directory exists.
     *
     * @return string[] elements removed
     */
    public function removeIfExists(): array;

    /**
     * Gets a child `DirInterface` for the added path.
     */
    public function getChild(string $path): self;
}
