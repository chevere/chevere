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

namespace Chevere\Components\Iterator;

use Chevere\Interfaces\Filesystem\DirInterface;
use RecursiveDirectoryIterator;

/**
 * @codeCoverageIgnore
 */
function recursiveDirectoryIteratorFor(DirInterface $dir, int $flags): RecursiveDirectoryIterator
{
    return new RecursiveDirectoryIterator(
        $dir->path()->toString(),
        $flags
    );
}
