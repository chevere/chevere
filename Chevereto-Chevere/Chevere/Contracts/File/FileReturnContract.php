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

use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\Exceptions\FileHandleException;
use Chevere\Components\File\Exceptions\FileWithoutContentsException;
use Chevere\Components\File\Exceptions\FileInvalidContentsException;
use Chevere\Components\File\Exceptions\FileUnableToPutException;

interface FileReturnContract
{
    const PHP_RETURN = "<?php\n\nreturn ";
    const PHP_RETURN_CHARS = 14;

    /**
     * Creates a new instance.
     *
     * @throws FileNotFoundException if the file doesn't exists
     */
    public function __construct(FilePhpContract $file);

    /**
     * Return an instance with no-strict flag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified no-strict flag.
     */
    public function withNoStrict(): FileReturnContract;

    /**
     * Provides access to the FileContract instance container in the FilePhpContract.
     */
    public function file(): FileContract;

    /**
     * Retrieves the file return (as-is).
     *
     * @throws FileNotFoundException        if the file doesn't exists
     * @throws FileHandleException          if unable to handle the file
     * @throws FileWithoutContentsException if the file doesn't contain anything
     * @throws FileInvalidContentsException if the file content is invalid
     */
    public function return();

    /**
     * Retrieves the usable variable after appling unserialize to all objects (if any).
     *
     * @throws FileNotFoundException        if the file doesn't exists
     * @throws FileHandleException          if unable to handle the file
     * @throws FileWithoutContentsException if the file doesn't contain anything
     * @throws FileInvalidContentsException if the file content is invalid
     */
    public function var();

    /**
     * Put $var into the file using var_export return.
     *
     * @throws FileNotFoundException    if the file doesn't exists
     * @throws FileUnableToPutException if unable to put the contents in the file
     */
    public function put($var): void;
}
