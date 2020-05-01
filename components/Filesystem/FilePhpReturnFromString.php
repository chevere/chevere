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

use Chevere\Components\Filesystem\Interfaces\FilePhpReturnFromStringInterface;
use Chevere\Components\Filesystem\Interfaces\FilePhpReturnInterface;

final class FilePhpReturnFromString implements FilePhpReturnFromStringInterface
{
    private FilePhpReturnInterface $filePhpReturn;

    public function __construct(string $absolute)
    {
        $this->filePhpReturn = new FilePhpReturn(new FilePhp(new File(new Path($absolute))));
    }

    public function filePhpReturn(): FilePhpReturnInterface
    {
        return $this->filePhpReturn;
    }
}
