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

use Chevere\Components\Filesystem\Interfaces\FileFromStringInterface;
use Chevere\Components\Filesystem\Interfaces\FileInterface;

/**
 * @codeCoverageIgnore
 */
final class FileFromString implements FileFromStringInterface
{
    private FileInterface $file;

    public function __construct(string $absolute)
    {
        $this->file = new File(new Path($absolute));
    }

    public function file(): FileInterface
    {
        return $this->file;
    }
}
