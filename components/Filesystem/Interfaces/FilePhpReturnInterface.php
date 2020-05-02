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

use Chevere\Components\Filesystem\Exceptions\FileHandleException;
use Chevere\Components\Filesystem\Exceptions\FileInvalidContentsException;
use Chevere\Components\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Components\Filesystem\Exceptions\FileUnableToPutException;
use Chevere\Components\Filesystem\Exceptions\FileWithoutContentsException;
use Chevere\Components\Variable\Interfaces\VariableExportInterface;

interface FilePhpReturnInterface
{
    const PHP_RETURN = '<?php return ';
    const PHP_RETURN_CHARS = 13;

    public function __construct(FilePhpInterface $phpFile);

    /**
     * Return an instance with the strict flag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified the strict flag.
     *
     * Strict validation refers to match the beginning of the file contents
     * against FilePhpReturnInterface::PHP_RETURN
     */
    public function withStrict(bool $strict): FilePhpReturnInterface;

    /**
     * Provides access to the FilePhpInterface instance.
     */
    public function filePhp(): FilePhpInterface;

    /**
     * Retrieves the file return (as-is).
     *
     * @throws FileNotExistsException        if the file doesn't exists
     * @throws FileHandleException          if unable to handle the file
     * @throws FileWithoutContentsException if the file doesn't contain anything
     * @throws FileInvalidContentsException if the file content is invalid
     */
    public function raw();

    /**
     * Retrieves the usable variable after appling unserialize to all objects (if any).
     *
     * @throws FileNotExistsException        if the file doesn't exists
     * @throws FileHandleException          if unable to handle the file
     * @throws FileWithoutContentsException if the file doesn't contain anything
     * @throws FileInvalidContentsException if the file content is invalid
     */
    public function var();

    /**
     * Put $var into the file using var_export return and strict format.
     *
     * @throws FileNotExistsException    if the file doesn't exists
     * @throws FileUnableToPutException if unable to put the contents in the file
     */
    public function put(VariableExportInterface $variableExport): void;
}
