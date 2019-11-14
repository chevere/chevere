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

namespace Chevere\Contracts\ArrayFile;

use Chevere\Components\Type\Type;
use Chevere\Components\Path\Exceptions\PathIsDirException;
use Chevere\Contracts\File\FileContract;
use Chevere\Contracts\File\FilePhpContract;

interface ArrayFileContract
{
    /**
     * Creates a new instance.
     *
     * @throws PathIsDirException if $path represents a directory
     */
    public function __construct(FilePhpContract $filePhp);

    /**
     * Return an instance with the specified ServicesContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouterContract.
     *
     * @param Type $type a Type that all array top level members must satisfy
     */
    public function withMembersType(Type $type): ArrayFileContract;

    /**
     * Provides access to the FileContract instance in FilePhpContract.
     */
    public function file(): FileContract;

    /**
     * Provides access to the file return array.
     */
    public function array(): array;
}
