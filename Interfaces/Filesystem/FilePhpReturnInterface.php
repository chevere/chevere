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

use Chevere\Exceptions\Filesystem\FileHandleException;
use Chevere\Exceptions\Filesystem\FileInvalidContentsException;
use Chevere\Exceptions\Filesystem\FileNotExistsException;
use Chevere\Exceptions\Filesystem\FileUnableToPutException;
use Chevere\Exceptions\Filesystem\FileWithoutContentsException;
use Chevere\Interfaces\Type\TypeInterface;
use Chevere\Interfaces\VarExportable\VarExportableInterface;

interface FilePhpReturnInterface
{
    const PHP_RETURN = '<?php return ';
    const PHP_RETURN_CHARS = 13;

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
     * @throws FileNotExistsException
     * @throws FileHandleException
     * @throws FileWithoutContentsException
     * @throws FileInvalidContentsException
     */
    public function raw();

    /**
     * Retrieves a PHP variable, applying unserialize to objects (if any).
     *
     * @throws FileNotExistsException
     * @throws FileHandleException
     * @throws FileWithoutContentsException
     * @throws FileInvalidContentsException
     */
    public function var();

    /**
     * Same as var, but checking the variable $type.
     *
     * @throws FileNotExistsException
     * @throws FileHandleException
     * @throws FileWithoutContentsException
     * @throws FileInvalidContentsException
     * @throws FileReturnInvalidTypeException
     */
    public function varType(TypeInterface $type);

    /**
     * Put $var into the file using var_export return and strict format.
     *
     * @throws FileNotExistsException
     * @throws FileUnableToPutException if unable to put the contents in the file
     */
    public function put(VarExportableInterface $varExportable): void;
}
