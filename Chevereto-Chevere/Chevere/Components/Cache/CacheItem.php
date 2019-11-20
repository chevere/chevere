<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Cache;

use Chevere\Contracts\Cache\CacheItemContract;
use Chevere\Contracts\File\FileReturnContract;

final class CacheItem implements CacheItemContract
{
    /** @var FileReturnContract */
    private $fileReturn;

    public function __construct(FileReturnContract $fileReturn)
    {
        $this->fileReturn = $fileReturn;
    }

    public function raw()
    {
        return $this->fileReturn->raw();
    }

    public function var()
    {
        return $this->fileReturn->var();
    }
}
