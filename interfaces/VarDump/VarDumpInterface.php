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

interface VarDumpInterface
{
    public function __construct(
        FormatterInterface $formatter,
        OutputterInterface $outputter
    );

    /**
     * Return an instance with the specified vars.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified vars.
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
     * Process the dump writing
     */
    public function process(WriterInterface $writer): void;

    /**
     * @return array the vars array.
     * @immutable
     */
    public function vars(): array;

    /**
     * @return int the shift value.
     */
    public function shift(): int;
}
