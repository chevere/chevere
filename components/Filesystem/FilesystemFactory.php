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
use Chevere\Exceptions\Filesystem\FilesystemFactoryException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\FileInterface;
use Chevere\Interfaces\Filesystem\FilePhpInterface;
use Chevere\Interfaces\Filesystem\FilesystemFactoryInterface;

/**
 * @codeCoverageIgnore
 */
final class FilesystemFactory implements FilesystemFactoryInterface
{
    public function __construct()
    {
    }

    public function getDirFromString(string $path): DirInterface
    {
        try {
            return new Dir(new Path($path));
        } catch (Exception $e) {
            throw new FilesystemFactoryException(
                $e->message(),
                $e->getCode(),
                $e
            );
        }
    }

    public function getFileFromString(string $path): FileInterface
    {
        try {
            return new File(new Path($path));
        } catch (Exception $e) {
            throw new FilesystemFactoryException(
                $e->message(),
                $e->getCode(),
                $e
            );
        }
    }

    public function getFilePhpFromString(string $path): FilePhpInterface
    {
        try {
            return new FilePhp($this->getFileFromString($path));
        } catch (Exception $e) {
            throw new FilesystemFactoryException(
                $e->message(),
                $e->getCode(),
                $e
            );
        }
    }

    public function getFilePhpReturnFromString(string $path): FilePhpReturn
    {
        try {
            return new FilePhpReturn($this->getFilePhpFromString($path));
        } catch (Exception $e) {
            throw new FilesystemFactoryException(
                $e->message(),
                $e->getCode(),
                $e
            );
        }
    }
}
