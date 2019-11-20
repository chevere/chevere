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

use RuntimeException;
use Chevere\Components\File\Exceptions\FileNotPhpException;
use Chevere\Components\File\Exceptions\FileNotFoundException;

interface FileCompileContract
{
    /**
     * Applies OPCache to the PHP file.
     *
     * @throws FileNotPhpException   if $file is not a PHP file
     * @throws FileNotFoundException if $file doesn't exists
     */
    public function __construct(FilePhpContract $filePhp);

    /**
     * Provides access to the FilePhpContract instance.
     */
    public function filePhp(): FilePhpContract;

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
