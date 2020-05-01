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

use Chevere\Components\Filesystem\Interfaces\DirFromStringInterface;
use Chevere\Components\Filesystem\Interfaces\DirInterface;

/**
 * @codeCoverageIgnore
 */
final class DirFromString implements DirFromStringInterface
{
    public function __construct(string $absolute)
    {
        $this->dir = new Dir(new Path($absolute));
    }

    public function dir(): DirInterface
    {
        return $this->dir;
    }
}
