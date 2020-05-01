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

use Chevere\Components\Filesystem\Interfaces\FilePhpFromStringInterface;
use Chevere\Components\Filesystem\Interfaces\FilePhpInterface;

final class FilePhpFromString implements FilePhpFromStringInterface
{
    private FilePhpInterface $filePhp;

    public function __construct(string $absolute)
    {
        $this->filePhp = new FilePhp(new File(new Path($absolute)));
    }

    public function filePhp(): FilePhpInterface
    {
        return $this->filePhp;
    }
}
