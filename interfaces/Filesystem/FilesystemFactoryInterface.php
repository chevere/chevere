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

namespace Chevere\Interfaces\Filesystem;

use Chevere\Components\Filesystem\FilePhpReturn;

interface FilesystemFactoryInterface
{
    public function __construct();

    public function getDirFromString(string $path): DirInterface;

    public function getFileFromString(string $path): FileInterface;

    public function getFilePhpFromString(string $path): FilePhpInterface;

    public function getFilePhpReturnFromString(string $path): FilePhpReturn;
}
