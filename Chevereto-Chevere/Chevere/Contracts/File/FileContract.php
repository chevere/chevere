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

namespace Chevere\Contracts\File;

use Chevere\Contracts\Path\PathContract;

interface FileContract
{
    /**
     * Creates a new instance.
     *
     * @throws InvalidArgumentException If the PathContract represents a directory.
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
     * Remove the file.
     *
     * @throws FileNotFoundException If the file doesn't exists.
     * @throws FileUnableToRemoveException If unable to remove the file.
     */
    public function remove(): void;

    /**
     * Put contents to the file. If the file doesn't exists it will be created.
     *
     * @throws RuntimeException If unable to put the file content.
     */
    public function put(string $contents): void;

    /**
     * Applies OPCache to the file (only if the file is a PHP script)
     *
     * @throws FileNotPhpException If attempt to compile a non-PHP file.
     * @throws FileNotFoundException If attempt to compile a file that doesn't exists.
     */
    public function compile(): void;
}
