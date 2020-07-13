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

use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Filesystem\FilesystemException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\FileInterface;
use Chevere\Interfaces\Filesystem\FilePhpInterface;

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function getDirFromString(string $path): DirInterface
{
    try {
        return new Dir(new Path($path));
    } catch (Exception $e) {
        throw new FilesystemException(
            $e->message(),
            $e->getCode(),
            $e
        );
    }
}

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function getFileFromString(string $path): FileInterface
{
    try {
        return new File(new Path($path));
    } catch (Exception $e) {
        throw new FilesystemException(
            $e->message(),
            $e->getCode(),
            $e
        );
    }
}

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function getFilePhpFromString(string $path): FilePhpInterface
{
    try {
        return new FilePhp(getFileFromString($path));
    } catch (Exception $e) {
        throw new FilesystemException(
            $e->message(),
            $e->getCode(),
            $e
        );
    }
}

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function getFilePhpReturnFromString(string $path): FilePhpReturn
{
    try {
        return new FilePhpReturn(getFilePhpFromString($path));
    } catch (Exception $e) {
        throw new FilesystemException(
            $e->message(),
            $e->getCode(),
            $e
        );
    }
}
