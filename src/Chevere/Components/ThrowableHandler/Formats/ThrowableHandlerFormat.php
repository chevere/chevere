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

use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerFormatInterface;
use Chevere\Interfaces\ThrowableHandler\ThrowableTraceFormatInterface;
use Chevere\Interfaces\VarDump\VarDumpFormatInterface;

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
        return '#' . ThrowableTraceFormatInterface::TAG_ENTRY_POS .
            ' ' . ThrowableTraceFormatInterface::TAG_ENTRY_FILE_LINE . "\n" .
            ThrowableTraceFormatInterface::TAG_ENTRY_CLASS .
            ThrowableTraceFormatInterface::TAG_ENTRY_TYPE .
            ThrowableTraceFormatInterface::TAG_ENTRY_FUNCTION;
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
