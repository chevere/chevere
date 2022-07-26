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

use Chevere\Filesystem\Exceptions\FileExistsException;
use Chevere\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Filesystem\Exceptions\FileUnableToCreateException;
use Chevere\Filesystem\Exceptions\FileUnableToGetException;
use Chevere\Filesystem\Exceptions\FileUnableToPutException;
use Chevere\Filesystem\Exceptions\FileUnableToRemoveException;
use Chevere\Filesystem\Exceptions\PathIsDirectoryException;

/**
 * Describes the component in charge of interacting with filesystem files.
 */
interface FileInterface
{
    public const CHECKSUM_ALGO = 'sha256';

    public const CHECKSUM_LENGTH = 64;

    /**
     * @throws PathIsDirectoryException
     */
    public function __construct(PathInterface $path);

    /**
     * Provides access to the PathInterface instance.
     */
    public function path(): PathInterface;

    /**
     * Returns a boolean indicating whether the file name is a PHP file.
     */
    public function isPhp(): bool;

    /**
     * Returns a boolean indicating whether the file exists.
     */
    public function exists(): bool;

    /**
     * @throws FileNotExistsException
     */
    public function assertExists(): void;

    /**
     * Retrieves the file checksum using the CHECKSUM_ALGO algorithm.
     *
     * @throws FileNotExistsException
     */
    public function getChecksum(): string;

    /**
     * Retrieves the file size using `filesize`.
     *
     * @throws FileNotExistsException
     */
    public function getSize(): int;

    /**
     * Retrieves the file contents.
     *
     * @throws FileNotExistsException
     * @throws FileUnableToGetException
     */
    public function getContents(): string;

    /**
     * Remove the file.
     *
     * @throws FileNotExistsException
     * @throws FileUnableToRemoveException
     */
    public function remove(): void;

    /**
     * @throws FileUnableToRemoveException
     */
    public function removeIfExists(): void;

    /**
     * Create the file.
     *
     * @throws FileExistsException
     * @throws FileUnableToCreateException
     */
    public function create(): void;

    /**
     * Create the file if it doesn't exists.
     *
     * @throws FileUnableToCreateException
     */
    public function createIfNotExists(): void;

    /**
     * Put contents to the file. If the file doesn't exists it will be created.
     *
     * @throws FileNotExistsException
     * @throws FileUnableToPutException
     */
    public function put(string $contents): void;
}
