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

use Chevere\Parameter\Interfaces\CastInterface;
use Chevere\VariableSupport\Interfaces\StorableVariableInterface;

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
     */
    public function get(): mixed;

    public function cast(): CastInterface;

    /**
     * Put `$storableVariable` into the file using var_export return and strict format.
     */
    public function put(StorableVariableInterface $storable): void;
}
