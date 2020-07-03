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

/**
 * Describes the component in charge of creating filesystem objects.
 */
interface FilesystemFactoryInterface
{
    public function __construct();

    /**
     * @throws FilesystemFactoryException
     */
    public function getDirFromString(string $path): DirInterface;

    /**
     * @throws FilesystemFactoryException
     */
    public function getFileFromString(string $path): FileInterface;

    /**
     * @throws FilesystemFactoryException
     */
    public function getFilePhpFromString(string $path): FilePhpInterface;

    /**
     * @throws FilesystemFactoryException
     */
    public function getFilePhpReturnFromString(string $path): FilePhpReturn;
}
