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

namespace Chevere\ThrowableHandler\Formats;

use Chevere\Trace\Interfaces\TraceFormatInterface;
use Chevere\VarDump\Formats\VarDumpConsoleFormat;
use Chevere\VarDump\Interfaces\VarDumpFormatInterface;
use Colors\Color;

final class ThrowableHandlerConsoleFormat extends ThrowableHandlerFormat
{
    public function getVarDumpFormat(): VarDumpFormatInterface
    {
        return new VarDumpConsoleFormat();
    }

    public function getTraceEntryTemplate(): string
    {
        return $this->wrapSectionTitle(
            '#' . TraceFormatInterface::TAG_ENTRY_POS
        ) .
            ' ' . TraceFormatInterface::TAG_ENTRY_FILE_LINE . "\n" .
            TraceFormatInterface::TAG_ENTRY_CLASS .
            TraceFormatInterface::TAG_ENTRY_TYPE .
            TraceFormatInterface::TAG_ENTRY_FUNCTION;
    }

    public function getHr(): string
    {
        return (string) (new Color(str_repeat('-', 60)))->blue();
    }

    public function wrapLink(string $value): string
    {
        return (string) (new Color($value))->underline()->fg('blue');
    }

    public function wrapSectionTitle(string $value): string
    {
        return (string) (new Color($value))->green();
    }
}
