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
    private FileReturnContract $fileReturn;

    /**
     * {@inheritdoc}
     */
    public function __construct(FileReturnContract $fileReturn)
    {
        $this->fileReturn = $fileReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function raw()
    {
        return $this->fileReturn->raw();
    }

    /**
     * {@inheritdoc}
     */
    public function var()
    {
        return $this->fileReturn->var();
    }
}
