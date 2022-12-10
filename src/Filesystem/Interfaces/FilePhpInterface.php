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

namespace Chevere\Filesystem\Interfaces;

use Chevere\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Filesystem\Exceptions\FileNotPhpException;
use Chevere\Throwable\Exceptions\RuntimeException;

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
     * Applies OPcache.
     *
     * @throws FileNotExistsException
     * @throws RuntimeException If OPcache is not enabled.
     */
    public function compileCache(): void;

    /**
     * Flushes OPcache.
     *
     * @throws RuntimeException If OPcache is not enabled.
     */
    public function flushCache(): void;
}
