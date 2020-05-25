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
        OutputterInterface $outpuStter
    );

    /**
     * Return an instance with the specified X.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified X.
     */
    public function withVars(...$vars): VarDumpInterface;

    /**
     * Return an instance with the specified shift.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified shift.
     *
     * Shift removes $shift traces from debug_backtrace()
     */
    public function withShift(int $shift): VarDumpInterface;

    /**
     * Streams the dump to the $writer
     */
    public function stream(): void;
}
