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

namespace Chevere\Interfaces\Bootstrap;

use Chevere\Exceptions\Filesystem\DirNotExistsException;
use Chevere\Exceptions\Filesystem\DirNotWritableException;
use Chevere\Interfaces\Filesystem\DirInterface;

/**
 * Describes a bootstrap providing access to its directory and time-related properties.
 */
interface BootstrapInterface
{
    /**
     * @throws DirNotExistsException
     * @throws DirNotWritableException
     */
    public function __construct(DirInterface $dir);

    /**
     * Provides access to the time registered on instance creation.
     */
    public function time(): int;

    /**
     * Provides access to the high-resolution time on instance creation.
     */
    public function hrTime(): int;

    /**
     * Provides access to dir.
     */
    public function dir(): DirInterface;
}
