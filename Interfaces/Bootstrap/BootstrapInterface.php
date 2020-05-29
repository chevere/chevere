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

use Chevere\Exceptions\Bootstrap\BootstrapDirException;
use Chevere\Interfaces\Filesystem\DirInterface;

interface BootstrapInterface
{
    /**
     * @throws BootstrapDirException
     */
    public function __construct(DirInterface $dir);

    /**
     * Provides access to the time() registered on instance creation.
     */
    public function time(): int;

    /**
     * Provides access to the hrtime(true) registered on instance creation.
     */
    public function hrTime(): int;

    /**
     * Provides access to the rootDir used on instance creation.
     */
    public function dir(): DirInterface;

    /**
     * Return an instance with the specified cli flag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified cli flag.
     */
    public function withCli(bool $bool): BootstrapInterface;

    /**
     * Returns a boolean indicating whether the instance has the cli flag.
     */
    public function isCli(): bool;
}
