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

namespace Chevere\Components\ArrayFile\Contracts;

use Chevere\Components\File\Contracts\FileContract;
use Chevere\Components\File\Contracts\FilePhpContract;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\Type\Contracts\TypeContract;

interface ArrayFileContract
{
    public function __construct(FilePhpContract $filePhp);

    /**
     * Return an instance with the specified ServicesContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouterContract.
     *
     * @param TypeContract $type a Type that all array top level members must satisfy
     *
     * @throws ArrayFileTypeException if one of the members doesn't match the specified $type
     */
    public function withMembersType(TypeContract $type): ArrayFileContract;

    /**
     * Provides access to the FileContract instance in FilePhpContract.
     */
    public function file(): FileContract;

    /**
     * Provides access to the file return array.
     */
    public function array(): array;
}
