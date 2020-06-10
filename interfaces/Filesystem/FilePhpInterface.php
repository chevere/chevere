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

namespace Chevere\Interfaces\Filesystem;

use Chevere\Exceptions\Filesystem\FileNotPhpException;

interface FilePhpInterface
{
    /**
     * Provides access to the FileInterface instance.
     */
    public function file(): FileInterface;

    /**
     * Applies OPCache.
     *
     * @throws RuntimeException If unable to cache file.
     */
    public function cache(): void;
}
