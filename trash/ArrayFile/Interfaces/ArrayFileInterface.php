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

namespace Chevere\Components\ArrayFile\Interfaces;

use Chevere\Components\Filesystem\Interfaces\File\FileInterface;
use Chevere\Components\Filesystem\Interfaces\File\PhpFileInterface;
use Chevere\Components\Type\Interfaces\TypeInterface;

interface ArrayFileInterface
{
    public function __construct(PhpFileInterface $filePhp);

    /**
     * Return an instance with the specified ServicesInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouterInterface.
     *
     * @param TypeInterface $type a Type that all array top level members must satisfy
     *
     * @throws ArrayFileTypeException if one of the members doesn't match the specified $type
     */
    public function withMembersType(TypeInterface $type): ArrayFileInterface;

    /**
     * Provides access to the FileInterface instance in FilePhpInterface.
     */
    public function file(): FileInterface;

    /**
     * Provides access to the file return array.
     */
    public function array(): array;
}
