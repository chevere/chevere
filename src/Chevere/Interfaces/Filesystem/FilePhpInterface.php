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

use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Filesystem\FileNotExistsException;
use Chevere\Exceptions\Filesystem\FileNotPhpException;

/**
 * Describes the component in charge of interact with `.php` files.
 */
interface FilePhpInterface
{
    /**
     * @throws FileNotPhpException
     */
    public function __construct(FileInterface $file);

    /**
     * Provides access to the FileInterface instance.
     */
    public function file(): FileInterface;

    /**
     * Applies OPCache.
     *
     * @throws FileNotExistsException
     * @throws RuntimeException If OPCache is not enabled.
     */
    public function cache(): void;

    /**
     * Flushes OPCache.
     *
     * @throws RuntimeException If OPCache is not enabled.
     */
    public function flush(): void;
}
