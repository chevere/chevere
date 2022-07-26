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

namespace Chevere\Iterator;

use Chevere\Filesystem\Interfaces\DirectoryInterface;
use RecursiveDirectoryIterator;

/**
 * @codeCoverageIgnore
 */
function recursiveDirectoryIteratorFor(DirectoryInterface $directory, int $flags): RecursiveDirectoryIterator
{
    return new RecursiveDirectoryIterator(
        $directory->path()->__toString(),
        $flags
    );
}
