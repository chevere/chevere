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

namespace Chevere\Trace\Interfaces;

use Chevere\Throwable\Exceptions\InvalidArgumentException;

/**
 * Describes the component in charge of describing a trace entry captured from `debug_backtrace()`.
 */
interface TraceEntryInterface
{
    /**
     * Known key properties
     */
    public const KEYS = ['file', 'line', 'function', 'class', 'type'];

    /**
     * Keys that MUST be present
     */
    public const MUST_HAVE_KEYS = ['function'];

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(array $entry);

    /**
     * Provides access to the filename.
     */
    public function file(): string;

    /**
     * Provides access to the line.
     */
    public function line(): int;

    /**
     * Provides access to the file plus line.
     *
     * ```php
     * return 'path_to_file:1';
     * ```
     */
    public function fileLine(): string;

    /**
     * Provides access to the function.
     */
    public function function(): string;

    /**
     * Provides access to the class (if any).
     */
    public function class(): string;

    /**
     * Provides access to the type, either `::` or '->'.
     */
    public function type(): string;

    /**
     * Provides access the arguments array.
     */
    public function args(): array;
}
