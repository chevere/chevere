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
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Filesystem\FilesystemException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\FileInterface;
use Chevere\Interfaces\Filesystem\FilePhpInterface;
use Chevere\Interfaces\Filesystem\FilePhpReturnInterface;
use Chevere\Interfaces\Message\MessageInterface;
use Chevere\Interfaces\Type\TypeInterface;
use Throwable;

/**
 * @codeCoverageIgnore
 */
function getFilesystemInstanceMessage(string $instance, string $path): MessageInterface
{
    return (new Message('Unable to create a %instance% for %path%'))
        ->code('%instance%', $instance)
        ->code('%path%', $path);
}

function tailDirPath(string $path): string
{
    $path .= substr($path, -1) == DIRECTORY_SEPARATOR
        ? ''
        : DIRECTORY_SEPARATOR;

    return $path;
}

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function dirForPath(string $path): DirInterface
{
    $path = tailDirPath($path);

    try {
        return new Dir(new Path($path));
    } catch (Throwable $e) {
        throw new FilesystemException(
            previous: $e,
            message: getFilesystemInstanceMessage(Dir::class, $path),
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
    } catch (Throwable $e) {
        throw new FilesystemException(
            previous: $e,
            message: getFilesystemInstanceMessage(File::class, $path),
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
    } catch (Throwable $e) {
        throw new FilesystemException(
            previous: $e,
            message: getFilesystemInstanceMessage(FilePhp::class, $path),
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
    } catch (Throwable $e) {
        throw new FilesystemException(
            previous: $e,
            message: getFilesystemInstanceMessage(FilePhpReturn::class, $path),
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
            previous: $e,
            message: (new Message('Unable to retrieve the expected variable of type %type%'))
                ->code('%type%', $type->typeHinting()),
        );
    }
}
