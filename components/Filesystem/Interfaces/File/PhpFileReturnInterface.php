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

namespace Chevere\Components\Filesystem\Interfaces\File;

use Chevere\Components\Filesystem\Exceptions\File\FileNotFoundException;
use Chevere\Components\Filesystem\Exceptions\File\FileHandleException;
use Chevere\Components\Filesystem\Exceptions\File\FileWithoutContentsException;
use Chevere\Components\Filesystem\Exceptions\File\FileInvalidContentsException;
use Chevere\Components\Filesystem\Exceptions\File\FileUnableToPutException;
use Chevere\Components\Variable\Interfaces\VariableExportInterface;

interface PhpFileReturnInterface
{
    const PHP_RETURN = '<?php return ';
    const PHP_RETURN_CHARS = 13;

    public function __construct(PhpFileInterface $phpFile);

    /**
     * Return an instance with no-strict flag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified no-strict flag.
     *
     * By default, a FileReturn must match the PHP_RETURN. The no-strict flag will allow to work with any PHP file
     * long as it returns something.
     */
    public function withStrict(bool $strict): PhpFileReturnInterface;

    /**
     * Provides access to the FilePhpInterface instance.
     */
    public function filePhp(): PhpFileInterface;

    /**
     * Retrieves the file return (as-is).
     *
     * @throws FileNotFoundException        if the file doesn't exists
     * @throws FileHandleException          if unable to handle the file
     * @throws FileWithoutContentsException if the file doesn't contain anything
     * @throws FileInvalidContentsException if the file content is invalid
     */
    public function raw();

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
    public function put(VariableExportInterface $variableExport): void;
}
