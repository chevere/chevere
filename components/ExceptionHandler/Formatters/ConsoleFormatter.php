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

namespace Chevere\Components\ExceptionHandler\Formatters;

use Chevere\Components\VarDump\Formatters\ConsoleFormatter as VarDumpFormatter;
use Chevere\Interfaces\ExceptionHandler\TraceFormatterInterface;
use Chevere\Interfaces\VarDump\FormatterInterface as VarDumpFormatterInterface;
use Colors\Color;

final class ConsoleFormatter extends AbstractFormatter
{
    public function getVarDumpFormatter(): VarDumpFormatterInterface
    {
        return new VarDumpFormatter;
    }

    public function getTraceEntryTemplate(): string
    {
        return $this->wrapSectionTitle('#' . TraceFormatterInterface::TAG_ENTRY_POS) . ' ' . TraceFormatterInterface::TAG_ENTRY_FILE_LINE . "\n"
            . TraceFormatterInterface::TAG_ENTRY_CLASS . TraceFormatterInterface::TAG_ENTRY_TYPE . TraceFormatterInterface::TAG_ENTRY_FUNCTION
            . '()';
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
