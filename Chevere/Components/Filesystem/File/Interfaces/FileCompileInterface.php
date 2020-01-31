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

use RuntimeException;
use Chevere\Components\Filesystem\File\Exceptions\FileNotFoundException;

interface FileCompileInterface
{
    public function __construct(FilePhpInterface $filePhp);

    /**
     * Provides access to the FilePhpInterface instance.
     */
    public function filePhp(): FilePhpInterface;

    /**
     * Compile the file.
     *
     * @throws FileNotFoundException if the file doesn't exists
     * @throws RuntimeException      if unable to compile
     */
    public function compile(): void;

    /**
     * Destroy the compile.
     *
     * @throws RuntimeException if unable to destroy
     */
    public function destroy(): void;
}
