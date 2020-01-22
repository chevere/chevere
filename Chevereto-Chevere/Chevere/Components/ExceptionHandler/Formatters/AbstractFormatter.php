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

use Chevere\Components\ExceptionHandler\Interfaces\FormatterInterface;
use Chevere\Components\ExceptionHandler\Interfaces\TraceInterface;
use Chevere\Components\VarDump\Interfaces\FormatterInterface as VarDumpFormatterInterface;

abstract class AbstractFormatter implements FormatterInterface
{
    protected VarDumpFormatterInterface $varDumpFormatter;

    /**
     * Creates a new instance.
     */
    final public function __construct()
    {
        $this->varDumpFormatter = $this->getVarDumpFormatter();
    }

    /**
     * {@inheritdoc}
     */
    final public function varDumpFormatter(): VarDumpFormatterInterface
    {
        return $this->varDumpFormatter;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getVarDumpFormatter(): VarDumpFormatterInterface;

    /**
     * {@inheritdoc}
     */
    public function getTraceEntryTemplate(): string
    {
        return '#' . TraceInterface::TAG_ENTRY_POS . ' ' . TraceInterface::TAG_ENTRY_FILE_LINE . "\n"
            . TraceInterface::TAG_ENTRY_CLASS . TraceInterface::TAG_ENTRY_TYPE . TraceInterface::TAG_ENTRY_FUNCTION
            . '()' . TraceInterface::TAG_ENTRY_ARGUMENTS;
    }

    /**
     * {@inheritdoc}
     */
    public function getHr(): string
    {
        return '------------------------------------------------------------';
    }

    /**
     * {@inheritdoc}
     */
    public function getLineBreak(): string
    {
        return "\n\n";
    }
}
