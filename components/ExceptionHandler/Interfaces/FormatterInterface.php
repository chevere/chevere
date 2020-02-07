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

namespace Chevere\Components\ExceptionHandler\Interfaces;

use Chevere\Components\VarDump\Interfaces\FormatterInterface as VarDumpFormatterInterface;

interface FormatterInterface
{
    /**
     * Provides access to the VarDumpFormatterInterface instance.
     */
    public function varDumpFormatter(): VarDumpFormatterInterface;

    /**
     * Get a new object implementing the VarDumpFormatterInterface.
     */
    public function getVarDumpFormatter(): VarDumpFormatterInterface;

    /**
     * Get the template used for each trace entry.
     *
     * @see TraceFormatterInterface for tag reference
     */
    public function getTraceEntryTemplate(): string;

    public function getHr(): string;

    public function getLineBreak(): string;

    public function wrapLink(string $value): string;

    public function wrapSectionTitle(string $value): string;

    public function wrapTitle(string $value): string;
}
