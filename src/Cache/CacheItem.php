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

namespace Chevere\Cache;

use Chevere\Cache\Interfaces\CacheItemInterface;
use Chevere\Filesystem\Interfaces\FilePhpReturnInterface;

final class CacheItem implements CacheItemInterface
{
    public function __construct(
        private FilePhpReturnInterface $phpFileReturn
    ) {
    }

    public function raw(): mixed
    {
        return $this->phpFileReturn->raw();
    }

    public function var(): mixed
    {
        return $this->phpFileReturn->var();
    }
}
