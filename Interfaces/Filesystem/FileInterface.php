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

use Chevere\Components\Filesystem\Exceptions\FileExistsException;
use Chevere\Components\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Components\Filesystem\Exceptions\FileUnableToCreateException;
use Chevere\Components\Filesystem\Exceptions\FileUnableToGetException;
use Chevere\Components\Filesystem\Exceptions\FileUnableToPutException;
use Chevere\Components\Filesystem\Exceptions\FileUnableToRemoveException;
use Chevere\Components\Filesystem\Exceptions\PathIsDirException;
use Chevere\Interfaces\Filesystem\PathInterface;

interface FileInterface
{
    const CHECKSUM_ALGO = 'sha256';
    const CHECKSUM_LENGTH = 64;

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
    public function checksum(): string;

    /**
     * Retrieves the file contents.
     *
     * @throws FileNotExistsException
     * @throws FileUnableToGetException
     */
    public function contents(): string;

    /**
     * Remove the file.
     *
     * @throws FileNotExistsException
     * @throws FileUnableToRemoveException
     */
    public function remove(): void;

    /**
     * Create the file.
     *
     * @throws FileExistsException
     * @throws FileUnableToCreateException
     */
    public function create(): void;

    /**
     * Put contents to the file. If the file doesn't exists it will be created.
     *
     * @throws FileNotExistsException
     * @throws FileUnableToPutException
     */
    public function put(string $contents): void;
}
