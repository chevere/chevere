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

use Stringable;

/**
 * Describes the component in charge of interact with filesystem paths.
 */
interface PathInterface extends Stringable
{
    /**
     * @return string absolute filesystem path
     */
    public function __toString(): string;

    /**
     * Asserts whether the path exists.
     */
    public function assertExists(): void;

    /**
     * Indicates whether the path exists.
     */
    public function exists(): bool;

    /**
     * Indicates whether the path is a directory and exists.
     */
    public function isDirectory(): bool;

    /**
     * Indicates whether the path is a file and exists.
     */
    public function isFile(): bool;

    /**
     * Wrapper for `\chmod`.
     */
    public function chmod(int $mode): void;

    /**
     * Wrapper for `\is_writeable`.
     */
    public function isWritable(): bool;

    /**
     * Wrapper for `\is_readable`.
     */
    public function isReadable(): bool;

    /**
     * Get a child instance for the target child path.
     */
    public function getChild(string $path): self;
}
