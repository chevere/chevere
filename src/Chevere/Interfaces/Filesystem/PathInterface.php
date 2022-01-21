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

use Chevere\Exceptions\Filesystem\PathDotSlashException;
use Chevere\Exceptions\Filesystem\PathDoubleDotsDashException;
use Chevere\Exceptions\Filesystem\PathExtraSlashesException;
use Chevere\Exceptions\Filesystem\PathIsNotDirectoryException;
use Chevere\Exceptions\Filesystem\PathNotAbsoluteException;
use Chevere\Exceptions\Filesystem\PathNotExistsException;
use Chevere\Exceptions\Filesystem\PathUnableToChmodException;
use Stringable;

/**
 * Describes the component in charge of interact with filesystem paths.
 */
interface PathInterface extends Stringable
{
    /**
     * @throws PathDotSlashException
     * @throws PathDoubleDotsDashException
     * @throws PathExtraSlashesException
     * @throws PathNotAbsoluteException
     */
    public function __construct(string $absolute);

    /**
     * @return string absolute filesystem path
     */
    public function __toString(): string;

    /**
     * Asserts whether the path exists.
     *
     * @throws PathNotExistsException
     */
    public function assertExists(): void;

    /**
     * Indicates whether the path exists.
     */
    public function exists(): bool;

    /**
     * Indicates whether the path is a directory and exists.
     */
    public function isDir(): bool;

    /**
     * Indicates whether the path is a file and exists.
     */
    public function isFile(): bool;

    /**
     * Wrapper for `\chmod`.
     *
     * @throws PathIsNotDirectoryException
     * @throws PathUnableToChmodException
     */
    public function chmod(int $mode): void;

    /**
     * Wrapper for `\is_writeable`.
     *
     * @throws PathNotExistsException
     */
    public function isWritable(): bool;

    /**
     * Wrapper for `\is_readable`.
     *
     * @throws PathNotExistsException
     */
    public function isReadable(): bool;

    /**
     * Get a child instance for the target child path.
     *
     * @throws PathDotSlashException
     * @throws PathDoubleDotsDashException
     * @throws PathExtraSlashesException
     * @throws PathNotAbsoluteException
     */
    public function getChild(string $path): self;
}
