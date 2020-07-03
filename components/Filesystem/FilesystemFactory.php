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
        return new Dir(new Path($path));
    }

    public function getFileFromString(string $path): FileInterface
    {
        return new File(new Path($path));
    }

    public function getFilePhpFromString(string $path): FilePhpInterface
    {
        return new FilePhp($this->getFileFromString($path));
    }

    public function getFilePhpReturnFromString(string $path): FilePhpReturn
    {
        return new FilePhpReturn($this->getFilePhpFromString($path));
    }
}
