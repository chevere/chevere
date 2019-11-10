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

interface FileCompileContract
{
    /**
     * Applies OPCache to the PHP file
     *
     * @throws FileNotPhpException If $file is not a PHP file.
     * @throws FileNotFoundException If $file doesn't exists.
     */
    public function __construct(FilePhpContract $file);

    /**
     * Compile the file.
     * 
     * @throws RuntimeException If unable to compile.
     */
    public function compile(): void;

    /**
     * Destroy the compile.
     * 
     * @throws RuntimeException If unable to destroy.
     */
    public function destroy(): void;
}
