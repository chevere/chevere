<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump\Contracts;

use BadMethodCallException;

interface DumperContract
{
    const BACKGROUND = '#132537';
    const BACKGROUND_SHADE = '#132537';
    const STYLE = 'font: 14px Consolas, monospace, sans-serif; line-height: 1.2; color: #ecf0f1; padding: 15px; margin: 10px 0; word-break: break-word; white-space: pre-wrap; background: ' . self::BACKGROUND . '; display: block; text-align: left; border: none; border-radius: 4px;';

    const OFFSET = 1;

    public function formatter(): FormatterContract;

    public function getFormatter(): FormatterContract;

    public function outputter(): OutputterContract;

    public function getOutputter(): OutputterContract;

    public function varDump(): VarDumpContract;

    /**
     * Dumps information of one or more variables.
     */
    public function dump(...$vars): void;

    /**
     * Provides access to the vars. Can be called only after calling dump.
     *
     * @throws BadMethodCallException if called before calling dump.
     */
    public function vars(): array;

    /**
     * Provides access to the debug backtrace. Can be called only after calling dump.
     *
     * @throws BadMethodCallException if called before calling dump.
     */
    public function debugBacktrace(): array;
}
