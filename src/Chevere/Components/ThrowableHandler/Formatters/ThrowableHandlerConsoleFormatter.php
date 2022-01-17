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

namespace Chevere\Components\ThrowableHandler\Formatters;

use Chevere\Components\VarDump\Format\VarDumpConsoleFormat as VarDumpFormatter;
use Chevere\Interfaces\ThrowableHandler\ThrowableTraceFormatterInterface;
use Chevere\Interfaces\VarDump\VarDumpFormatInterface;
use Colors\Color;

final class ThrowableHandlerConsoleFormatter extends ThrowableHandlerFormatter
{
    public function getVarDumpFormatter(): VarDumpFormatInterface
    {
        return new VarDumpFormatter();
    }

    public function getTraceEntryTemplate(): string
    {
        return $this->wrapSectionTitle(
            '#' . ThrowableTraceFormatterInterface::TAG_ENTRY_POS
        ) .
            ' ' . ThrowableTraceFormatterInterface::TAG_ENTRY_FILE_LINE . "\n" .
            ThrowableTraceFormatterInterface::TAG_ENTRY_CLASS .
            ThrowableTraceFormatterInterface::TAG_ENTRY_TYPE .
            ThrowableTraceFormatterInterface::TAG_ENTRY_FUNCTION;
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
