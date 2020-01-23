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

namespace Chevere\Components\VarDump\Interfaces;

interface OutputterInterface
{
    public function withDumper(DumperInterface $dumper): OutputterInterface;

    public function dumper(): DumperInterface;

    public function prepare(string $output): string;

    public function process(): OutputterInterface;

    public function toString(): string;
}
