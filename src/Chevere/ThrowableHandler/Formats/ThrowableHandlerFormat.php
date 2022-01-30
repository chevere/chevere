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

use Chevere\ThrowableHandler\Interfaces\ThrowableHandlerFormatInterface;
use Chevere\Trace\Interfaces\TraceFormatInterface;
use Chevere\VarDump\Interfaces\VarDumpFormatInterface;

abstract class ThrowableHandlerFormat implements ThrowableHandlerFormatInterface
{
    protected VarDumpFormatInterface $varDumpFormatter;

    final public function __construct()
    {
        $this->varDumpFormatter = $this->getVarDumpFormat();
    }

    final public function varDumpFormat(): VarDumpFormatInterface
    {
        return $this->varDumpFormatter;
    }

    abstract public function getVarDumpFormat(): VarDumpFormatInterface;

    public function getTraceEntryTemplate(): string
    {
        return '#' . TraceFormatInterface::TAG_ENTRY_POS .
            ' ' . TraceFormatInterface::TAG_ENTRY_FILE_LINE . "\n" .
            TraceFormatInterface::TAG_ENTRY_CLASS .
            TraceFormatInterface::TAG_ENTRY_TYPE .
            TraceFormatInterface::TAG_ENTRY_FUNCTION;
    }

    public function getHr(): string
    {
        return '------------------------------------------------------------';
    }

    public function getLineBreak(): string
    {
        return "\n\n";
    }

    public function wrapLink(string $value): string
    {
        return $value;
    }

    public function wrapHidden(string $value): string
    {
        return $value;
    }

    public function wrapSectionTitle(string $value): string
    {
        return $value;
    }

    public function wrapTitle(string $value): string
    {
        return $value;
    }
}
