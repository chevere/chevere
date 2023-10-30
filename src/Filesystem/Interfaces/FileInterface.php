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
 * Describes the component in charge of interacting with filesystem files.
 */
interface FileInterface
{
    public const CHECKSUM_ALGO = 'sha256';

    public const CHECKSUM_LENGTH = 64;

    /**
     * Provides access to the PathInterface instance.
     */
    public function path(): PathInterface;

    /**
     * Indicates whether the file name is a PHP file.
     */
    public function isPhp(): bool;

    /**
     * Indicates whether the file exists.
     */
    public function exists(): bool;

    public function assertExists(): void;

    /**
     * Retrieves the file checksum using the CHECKSUM_ALGO algorithm.
     */
    public function getChecksum(): string;

    /**
     * Retrieves the file size using `filesize`.
     */
    public function getSize(): int;

    /**
     * Retrieves the file contents.
     */
    public function getContents(): string;

    /**
     * Remove the file.
     */
    public function remove(): void;

    public function removeIfExists(): void;

    /**
     * Create the file.
     */
    public function create(): void;

    /**
     * Create the file if it doesn't exists.
     */
    public function createIfNotExists(): void;

    /**
     * Put contents to the file. If the file doesn't exists it will be created.
     */
    public function put(string $contents): void;
}
