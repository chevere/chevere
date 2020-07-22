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
use Chevere\Exceptions\Filesystem\FilesystemException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\FileInterface;
use Chevere\Interfaces\Filesystem\FilePhpInterface;

/**
 * @codeCoverageIgnore
 * @throws FilesystemException
 */
function dirForString(string $path): DirInterface
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
function fileForString(string $path): FileInterface
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
function filePhpForString(string $path): FilePhpInterface
{
    try {
        return new FilePhp(fileForString($path));
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
function filePhpReturnForString(string $path): FilePhpReturn
{
    try {
        return new FilePhpReturn(filePhpForString($path));
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
