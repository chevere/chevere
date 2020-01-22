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
use Chevere\Components\VarDump\Interfaces\FormatterInterface as VarDumpFormatterInterface;
use Chevere\Components\VarDump\Formatters\HtmlFormatter as VarDumpFormatter;

final class HtmlFormatter extends AbstractFormatter
{
    public function getVarDumpFormatter(): VarDumpFormatterInterface
    {
        return new VarDumpFormatter;
    }

    public function getTraceEntryTemplate(): string
    {
        return '<div class="pre pre--stack-entry ' . TraceInterface::TAG_ENTRY_CSS_EVEN_CLASS . '">#' . TraceInterface::TAG_ENTRY_POS . ' '
            . TraceInterface::TAG_ENTRY_FILE_LINE . "\n" . TraceInterface::TAG_ENTRY_CLASS
            . TraceInterface::TAG_ENTRY_TYPE . TraceInterface::TAG_ENTRY_FUNCTION . '()'
            . TraceInterface::TAG_ENTRY_ARGUMENTS . '</div>';
    }

    public function getHr(): string
    {
        return '<div class="hr"><span>------------------------------------------------------------</span></div>';
    }
}
