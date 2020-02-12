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

use Chevere\Components\Writers\Interfaces\StreamWriterInterface;

interface OutputterInterface
{
    public function __construct(VarDumperInterface $dumper, StreamWriterInterface $streamWriter);

    public function varDumper(): VarDumperInterface;

    public function prepare(): void;

    public function emit(): void;
}
