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

namespace Chevere\Components\Cache\Interfaces;

use Chevere\Components\Filesystem\Interfaces\File\PhpFileReturnInterface;

interface CacheItemInterface
{
    public function __construct(PhpFileReturnInterface $phpFileReturn);

    /**
     * Provides raw access to the FileReturnInterface "as-is"
     * @return mixed
     */
    public function raw();

    /**
     * Provides access to the FileReturnInterface file return variable
     * @return mixed
     */
    public function var();
}
