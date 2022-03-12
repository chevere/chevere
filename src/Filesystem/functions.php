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
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Filesystem\Interfaces\FileInterface;
use Chevere\Filesystem\Interfaces\FilePhpInterface;
use Chevere\Filesystem\Interfaces\FilePhpReturnInterface;
use Chevere\Message\Interfaces\MessageInterface;
use Chevere\Message\Message;
use Chevere\Throwable\Exceptions\LogicException;
use Chevere\Type\Interfaces\TypeInterface;
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
function dirForPath(string $path): DirInterface
{
    $path = tailDirPath($path);

    return new Dir(new Path($path));
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
function varForFilePhpReturn(FilePhpReturnInterface $filePhpReturn, TypeInterface $type): mixed
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
