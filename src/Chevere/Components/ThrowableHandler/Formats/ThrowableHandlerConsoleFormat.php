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

namespace Chevere\Components\ThrowableHandler\Formats;

use Chevere\Components\VarDump\Formats\VarDumpConsoleFormat;
use Chevere\Interfaces\ThrowableHandler\ThrowableTraceFormatInterface;
use Chevere\Interfaces\VarDump\VarDumpFormatInterface;
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
            '#' . ThrowableTraceFormatInterface::TAG_ENTRY_POS
        ) .
            ' ' . ThrowableTraceFormatInterface::TAG_ENTRY_FILE_LINE . "\n" .
            ThrowableTraceFormatInterface::TAG_ENTRY_CLASS .
            ThrowableTraceFormatInterface::TAG_ENTRY_TYPE .
            ThrowableTraceFormatInterface::TAG_ENTRY_FUNCTION;
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
