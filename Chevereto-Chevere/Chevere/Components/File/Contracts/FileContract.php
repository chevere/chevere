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

namespace Chevere\Components\File\Contracts;

use Chevere\Components\File\Exceptions\FileExistsException;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\Exceptions\FileUnableToCreateException;
use Chevere\Components\File\Exceptions\FileUnableToRemoveException;
use Chevere\Contracts\Path\PathContract;
use Chevere\Components\File\Exceptions\FileUnableToPutException;
use Chevere\Components\File\Exceptions\FileUnableToGetException;
use Chevere\Components\Path\Exceptions\PathIsDirException;

interface FileContract
{
    const CHECKSUM_ALGO = 'sha256';

    /**
     * Creates a new instance.
     *
     * @throws PathIsDirException if the PathContract represents a directory
     */
    public function __construct(PathContract $path);

    /**
     * Provides access to the PathContract instance.
     */
    public function path(): PathContract;

    /**
     * Returns a boolean indicating whether the file represents a PHP file.
     */
    public function isPhp(): bool;

    /**
     * Returns a boolean indicating whether the file exists.
     */
    public function exists(): bool;

    /**
     * Throws an exception if the file doesn't exists.
     *
     * @throws FileNotFoundException if the file doesn't exists
     */
    public function assertExists(): void;

    /**
     * Retrieves the file checksum using the CHECKSUM_ALGO algorithm.
     *
     * @throws FileNotFoundException if the file doesn't exists
     */
    public function checksum(): string;

    /**
     * Retrieves the file contents.
     *
     * @throws FileNotFoundException    if the file doesn't exists
     * @throws FileUnableToGetException if unable to read the contents of the file
     */
    public function contents(): string;

    /**
     * Remove the file.
     *
     * @throws FileNotFoundException       if the file doesn't exists
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
     * @throws FileNotFoundException    if the file doesn't exists
     * @throws FileUnableToPutException if unable to put the file content
     */
    public function put(string $contents): void;
}
