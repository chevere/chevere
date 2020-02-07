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

namespace Chevere\Components\Bootstrap\Interfaces;

use Chevere\Components\Console\Interfaces\ConsoleInterface;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;

interface BootstrapInterface
{
    /**
     * @throws BootstrapDirException
     */
    public function __construct(DirInterface $rootDir, DirInterface $app);

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
    public function rootDir(): DirInterface;

    /**
     * Provides access to the appDir used on instance creation.
     */
    public function appDir(): DirInterface;

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

    /**
     * Return an instance with the specified ConsoleInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified ConsoleInterface.
     */
    public function withConsole(ConsoleInterface $console): BootstrapInterface;

    /**
     * Returns a boolean indicating whether the instance has a ConsoleInterface.
     */
    public function hasConsole(): bool;

    /**
     * Provides access to the ConsoleInterface instance.
     */
    public function console(): ConsoleInterface;

    /**
     * Return an instance with the specified dev flag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified dev flag.
     */
    public function withDev(bool $bool): BootstrapInterface;

    /**
     * Returns a boolean indicating whether the instance has a the dev flag.
     */
    public function isDev(): bool;
}
