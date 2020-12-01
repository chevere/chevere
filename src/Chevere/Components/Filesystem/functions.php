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

namespace Chevere\Components\Filesystem;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Exceptions\Filesystem\FileReturnInvalidTypeException;
use Chevere\Exceptions\Filesystem\FilesystemException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\FileInterface;
use Chevere\Interfaces\Filesystem\FilePhpInterface;
use Chevere\Interfaces\Filesystem\FilePhpReturnInterface;
use Chevere\Interfaces\Type\TypeInterface;
use Throwable;

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function dirForPath(string $path): DirInterface
{
    try {
        return new Dir(new Path($path));
    } catch (Exception $e) {
        throw new FilesystemException(
            (new Message('Unable to create a %instance% for %path%'))
                ->code('%instance%', Dir::class)
                ->code('%path%', $path),
            0,
            $e
        );
    }
}

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function fileForPath(string $path): FileInterface
{
    try {
        return new File(new Path($path));
    } catch (Exception $e) {
        throw new FilesystemException(
            (new Message('Unable to create a %instance% for %path%'))
                ->code('%instance%', File::class)
                ->code('%path%', $path),
            0,
            $e
        );
    }
}

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function filePhpForPath(string $path): FilePhpInterface
{
    try {
        return new FilePhp(fileForPath($path));
    } catch (Exception $e) {
        throw new FilesystemException(
            (new Message('Unable to create a %instance% for %path%'))
                ->code('%instance%', FilePhp::class)
                ->code('%path%', $path),
            0,
            $e
        );
    }
}

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function filePhpReturnForPath(string $path): FilePhpReturn
{
    try {
        return new FilePhpReturn(filePhpForPath($path));
    } catch (Exception $e) {
        throw new FilesystemException(
            (new Message('Unable to create a %instance% for %path%'))
                ->code('%instance%', FilePhpReturn::class)
                ->code('%path%', $path),
            0,
            $e
        );
    }
}

/**
 * @codeCoverageIgnore
 * @throws LogicException
 */
function varForFilePhpReturn(FilePhpReturnInterface $filePhpReturn, TypeInterface $type)
{
    try {
        return $filePhpReturn->varType($type);
    } catch (Throwable $e) {
        throw new LogicException(
            (new Message('Unable to retrieve the expected variable of type %type%'))
                ->code('%type%', $type->typeHinting()),
            0,
            $e
        );
    }
}
