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

namespace Chevere\Contracts\Bootstrap;

interface BootstrapContract
{
    public function __construct(string $documentRoot);

    public function time(): int;

    public function hrTime(): int;

    public function documentRoot(): string;

    public function rootPath(): string;

    public function appPath(): string;

    public function withCli(bool $bool): BootstrapContract;

    public function cli(): bool;

    public function withConsole(bool $bool): BootstrapContract;

    public function console(): bool;

    public function withDev(bool $bool): BootstrapContract;

    public function dev(): bool;

    public function withAppAutoloader(string $namespace): BootstrapContract;
}
