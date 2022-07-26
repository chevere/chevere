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

namespace Chevere\Filesystem;

use Chevere\Filesystem\Exceptions\FilesystemException;
use Chevere\Filesystem\Interfaces\DirectoryInterface;
use Chevere\Filesystem\Interfaces\FileInterface;
use Chevere\Filesystem\Interfaces\FilePhpInterface;
use Chevere\Filesystem\Interfaces\FilePhpReturnInterface;
use Chevere\Message\Interfaces\MessageInterface;
use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\LogicException;
use Chevere\Type\Interfaces\TypeInterface;
use Throwable;

/**
 * @codeCoverageIgnore
 */
function getFilesystemInstanceMessage(string $instance, string $path): MessageInterface
{
    return message('Unable to create a %instance% for %path%')
        ->withCode('%instance%', $instance)
        ->withCode('%path%', $path);
}

function tailDirectoryPath(string $path): string
{
    if (substr($path, -1) === '\\') {
        $path = substr($path, 0, -1);
    }
    $path .= substr($path, -1) == '/'
        ? ''
        : '/';

    return $path;
}

/**
 * @codeCoverageIgnore
 */
function directoryForPath(string $path): DirectoryInterface
{
    $path = tailDirectoryPath($path);

    return new Directory(new Path($path));
}

/**
 * @codeCoverageIgnore
 */
function fileForPath(string $path): FileInterface
{
    return new File(new Path($path));
}

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function filePhpForPath(string $path): FilePhpInterface
{
    return new FilePhp(fileForPath($path));
}

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function filePhpReturnForPath(string $path): FilePhpReturnInterface
{
    return new FilePhpReturn(filePhpForPath($path));
}

/**
 * @codeCoverageIgnore
 * @throws LogicException
 */
function variableForFilePhpReturn(FilePhpReturnInterface $filePhpReturn, TypeInterface $type): mixed
{
    try {
        return $filePhpReturn->variableTyped($type);
    } catch (Throwable $e) {
        throw new LogicException(
            previous: $e,
            message: message('Unable to retrieve the expected variable of type %type%')
                ->withCode('%type%', $type->typeHinting()),
        );
    }
}
