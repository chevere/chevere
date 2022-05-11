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

use Chevere\Filesystem\Exceptions\FileHandleException;
use Chevere\Filesystem\Exceptions\FileInvalidContentsException;
use Chevere\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Filesystem\Exceptions\FileReturnInvalidTypeException;
use Chevere\Filesystem\Exceptions\FileUnableToGetException;
use Chevere\Filesystem\Exceptions\FileUnableToPutException;
use Chevere\Filesystem\Exceptions\FileWithoutContentsException;
use Chevere\Throwable\Exceptions\RuntimeException;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarSupport\Interfaces\VarStorableInterface;

/**
 * Describes the component in charge of interact with `.php` files that return a variable.
 *
 * ```php
 * <?php return 'Hello World!';
 * ```;
 */
interface FilePhpReturnInterface
{
    public const PHP_RETURN = '<?php return ';

    public const PHP_RETURN_CHARS = 13;

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
     * @throws FileUnableToGetException
     * @throws RuntimeException
     */
    public function raw(): mixed;

    /**
     * Retrieves a PHP variable, applying unserialize to objects (if any).
     *
     * @throws FileNotExistsException
     * @throws FileHandleException
     * @throws FileWithoutContentsException
     * @throws FileInvalidContentsException
     * @throws FileUnableToGetException
     * @throws RuntimeException
     */
    public function var(): mixed;

    /**
     * Same as `var()`, but checking the variable `$type`.
     *
     * @throws FileNotExistsException
     * @throws FileHandleException
     * @throws FileWithoutContentsException
     * @throws FileInvalidContentsException
     * @throws FileUnableToGetException
     * @throws RuntimeException
     * @throws FileReturnInvalidTypeException
     */
    public function varType(TypeInterface $type): mixed;

    /**
     * Put `$var` into the file using var_export return and strict format.
     *
     * @throws FileNotExistsException
     * @throws FileUnableToPutException
     */
    public function put(VarStorableInterface $varStorable): void;
}