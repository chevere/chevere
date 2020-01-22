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

namespace Chevere\Components\VarDump\Interfaces;

use BadMethodCallException;

interface DumperInterface
{
    const BACKGROUND = '#132537';
    const BACKGROUND_SHADE = '#132537';
    /** @var string Dump style, no double quotes. */
    const STYLE = "font: 14px 'Fira Code Retina', 'Operator Mono', Inconsolata, Consolas,
    monospace, sans-serif, sans-serif; line-height: 1.2; color: #ecf0f1; padding: 15px; margin: 10px 0; word-break: break-word; white-space: pre-wrap; background: " . self::BACKGROUND . '; display: block; text-align: left; border: none; border-radius: 4px;';

    const OFFSET = 1;

    public function formatter(): FormatterInterface;

    public function getFormatter(): FormatterInterface;

    public function outputter(): OutputterInterface;

    public function getOutputter(): OutputterInterface;

    /**
     * Return an instance with the specified vars.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified vars.
     */
    public function withVars(...$vars): DumperInterface;

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
