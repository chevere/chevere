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

namespace Chevere\Components\VarDump\Contracts;

interface OutputterContract
{
    public function withDumper(DumperContract $dumper): OutputterContract;

    public function dumper(): DumperContract;

    public function prepare(): OutputterContract;

    public function process(): OutputterContract;

    public function toString(): string;

    public function printOutput(): void;
}
