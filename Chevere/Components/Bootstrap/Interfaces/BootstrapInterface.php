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
use Chevere\Components\Filesystem\Dir\Interfaces\DirInterface;

interface BootstrapInterface
{
    public function __construct(DirInterface $rootDir, DirInterface $app);

    public function time(): int;

    public function hrTime(): int;

    public function rootDir(): DirInterface;

    public function appDir(): DirInterface;

    public function withCli(bool $bool): BootstrapInterface;

    public function isCli(): bool;

    public function withConsole(ConsoleInterface $console): BootstrapInterface;

    public function hasConsole(): bool;

    public function console(): ConsoleInterface;

    public function withDev(bool $bool): BootstrapInterface;

    public function isDev(): bool;

    public function withAppAutoloader(string $namespace): BootstrapInterface;
}
