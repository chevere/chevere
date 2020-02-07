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
    public function __construct(VarDumperInterface $dumper);

    public function varDumper(): VarDumperInterface;

    public function prepare(string $output): string;

    public function toString(): string;
}
