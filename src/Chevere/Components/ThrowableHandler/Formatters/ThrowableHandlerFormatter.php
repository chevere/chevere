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

use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerFormatterInterface;
use Chevere\Interfaces\ThrowableHandler\ThrowableTraceFormatterInterface;
use Chevere\Interfaces\VarDump\VarDumpFormatterInterface;

abstract class ThrowableHandlerFormatter implements ThrowableHandlerFormatterInterface
{
    protected VarDumpFormatterInterface $varDumpFormatter;

    final public function __construct()
    {
        $this->varDumpFormatter = $this->getVarDumpFormatter();
    }

    final public function varDumpFormatter(): VarDumpFormatterInterface
    {
        return $this->varDumpFormatter;
    }

    abstract public function getVarDumpFormatter(): VarDumpFormatterInterface;

    public function getTraceEntryTemplate(): string
    {
        return '#' . ThrowableTraceFormatterInterface::TAG_ENTRY_POS .
            ' ' . ThrowableTraceFormatterInterface::TAG_ENTRY_FILE_LINE . "\n" .
            ThrowableTraceFormatterInterface::TAG_ENTRY_CLASS .
            ThrowableTraceFormatterInterface::TAG_ENTRY_TYPE .
            ThrowableTraceFormatterInterface::TAG_ENTRY_FUNCTION;
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
