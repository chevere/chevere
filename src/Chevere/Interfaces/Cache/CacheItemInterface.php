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

namespace Chevere\Interfaces\Cache;

use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Filesystem\FilePhpReturnInterface;

/**
 * Describes the component that defines a cache item.
 */
interface CacheItemInterface
{
    public function __construct(FilePhpReturnInterface $phpFileReturn);

    /**
     * Provides raw access to the cache value "as-is".
     *
     * @throws RuntimeException
     */
    public function raw();

    /**
     * Provides access to the cache PHP variable.
     *
     * @throws RuntimeException
     */
    public function var();
}
