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

use Chevere\Components\ExceptionHandler\Interfaces\TraceInterface;
use Chevere\Components\VarDump\Formatters\ConsoleFormatter as VarDumpFormatter;
use Chevere\Components\VarDump\Interfaces\FormatterInterface as VarDumpFormatterInterface;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

final class ConsoleFormatter extends AbstractFormatter
{
    public function getVarDumpFormatter(): VarDumpFormatterInterface
    {
        return new VarDumpFormatter;
    }

    public function getTraceEntryTemplate(): string
    {
        return (new ConsoleColor)->apply('green', '#' . TraceInterface::TAG_ENTRY_POS) . ' ' . TraceInterface::TAG_ENTRY_FILE_LINE . "\n"
            . TraceInterface::TAG_ENTRY_CLASS . TraceInterface::TAG_ENTRY_TYPE . TraceInterface::TAG_ENTRY_FUNCTION
            . '()' . TraceInterface::TAG_ENTRY_ARGUMENTS;
    }

    public function getHr(): string
    {
        return (new ConsoleColor)->apply('blue', '------------------------------------------------------------');
    }
}
