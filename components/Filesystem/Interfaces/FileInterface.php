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

namespace Chevere\Components\Filesystem\Interfaces;

use Chevere\Components\Filesystem\Exceptions\FileExistsException;
use Chevere\Components\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Components\Filesystem\Exceptions\FileUnableToCreateException;
use Chevere\Components\Filesystem\Exceptions\FileUnableToGetException;
use Chevere\Components\Filesystem\Exceptions\FileUnableToPutException;
use Chevere\Components\Filesystem\Exceptions\FileUnableToRemoveException;
use Chevere\Components\Filesystem\Exceptions\PathIsDirException;
use Chevere\Components\Filesystem\Interfaces\PathInterface;

interface FileInterface
{
    const CHECKSUM_ALGO = 'sha256';
    const CHECKSUM_LENGTH = 64;

    /**
     * @throws PathIsDirException if the $path represents a directory
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
     * Throws an exception if the file doesn't exists.
     *
     * @throws FileNotExistsException if the file doesn't exists
     */
    public function assertExists(): void;

    /**
     * Retrieves the file checksum using the CHECKSUM_ALGO algorithm.
     *
     * @throws FileNotExistsException if the file doesn't exists
     */
    public function checksum(): string;

    /**
     * Retrieves the file contents.
     *
     * @throws FileNotExistsException    if the file doesn't exists
     * @throws FileUnableToGetException if unable to read the contents of the file
     */
    public function contents(): string;

    /**
     * Remove the file.
     *
     * @throws FileNotExistsException       if the file doesn't exists
     * @throws FileUnableToRemoveException if unable to remove the file
     */
    public function remove(): void;

    /**
     * Create the file.
     *
     * @throws FileExistsException         if the file alread exists
     * @throws FileUnableToCreateException if unable to remove the file
     */
    public function create(): void;

    /**
     * Put contents to the file. If the file doesn't exists it will be created.
     *
     * @throws FileNotExistsException    if the file doesn't exists
     * @throws FileUnableToPutException if unable to put the file content
     */
    public function put(string $contents): void;
}
