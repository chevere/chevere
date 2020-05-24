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

namespace Chevere\Interfaces\VarDump;

use Chevere\Interfaces\Writers\WriterInterface;

/**
 * A context-aware var dump utility
 */
interface VarDumpInterface
{
    public function __construct(
        WriterInterface $writer,
        FormatterInterface $formatter,
        OutputterInterface $outputter
    );

    public function withVars(...$vars): VarDumpInterface;

    public function withShift(int $shift): VarDumpInterface;

    public function stream(): void;
}
