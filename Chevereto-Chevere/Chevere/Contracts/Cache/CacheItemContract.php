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

namespace Chevere\Contracts\Cache;

use Chevere\Contracts\File\FileReturnContract;

interface CacheItemContract
{
    public function __construct(FileReturnContract $fileReturn);

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
