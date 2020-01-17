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

interface BootstrapInterface
{
    public function __construct(string $documentRoot);

    public function time(): int;

    public function hrTime(): int;

    public function documentRoot(): string;

    public function rootPath(): string;

    public function appPath(): string;

    public function withCli(bool $bool): BootstrapInterface;

    public function isCli(): bool;

    public function withConsole(bool $bool): BootstrapInterface;

    public function isConsole(): bool;

    public function withDev(bool $bool): BootstrapInterface;

    public function isDev(): bool;

    public function withAppAutoloader(string $namespace): BootstrapInterface;
}
