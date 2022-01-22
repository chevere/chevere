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

use Chevere\Components\VarDump\Formats\VarDumpHtmlFormat;
use Chevere\Interfaces\ThrowableHandler\ThrowableTraceFormatInterface;
use Chevere\Interfaces\VarDump\VarDumpFormatInterface;

final class ThrowableHandlerHtmlFormat extends ThrowableHandlerFormat
{
    public function getVarDumpFormat(): VarDumpFormatInterface
    {
        return new VarDumpHtmlFormat();
    }

    public function getTraceEntryTemplate(): string
    {
        return '<div class="pre pre--stack-entry ' .
            ThrowableTraceFormatInterface::TAG_ENTRY_CSS_EVEN_CLASS . '">#' .
            ThrowableTraceFormatInterface::TAG_ENTRY_POS . ' ' .
            ThrowableTraceFormatInterface::TAG_ENTRY_FILE_LINE . "\n" .
            ThrowableTraceFormatInterface::TAG_ENTRY_CLASS .
            ThrowableTraceFormatInterface::TAG_ENTRY_TYPE .
            ThrowableTraceFormatInterface::TAG_ENTRY_FUNCTION .
            '</div>';
    }

    public function getHr(): string
    {
        return '<div class="hr"><span>'
            . str_repeat('-', 60)
            . '</span></div>';
    }

    public function getLineBreak(): string
    {
        return "\n<br>\n";
    }
    
    public function wrapHidden(string $value): string
    {
        return '<span class="hide">' . $value . '</span>';
    }
    
    public function wrapSectionTitle(string $value): string
    {
        return '<div class="title">'
            . str_replace('# ', $this->wrapHidden('#&nbsp;'), $value)
            . '</div>';
    }

    public function wrapTitle(string $value): string
    {
        return '<div class="title title--scream">' . $value . '</div>';
    }
}
