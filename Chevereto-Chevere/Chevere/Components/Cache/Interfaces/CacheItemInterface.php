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

namespace Chevere\Components\Cache\Interfaces;

use Chevere\Components\File\Interfaces\FileReturnInterface;

interface CacheItemInterface
{
    public function __construct(FileReturnInterface $fileReturn);

    /**
     * Provides raw access to the FileReturnContract "as-is"
     * @return mixed
     */
    public function raw();

    /**
     * Provides access to the FileReturnContract file return variable
     * @return mixed
     */
    public function var();
}
