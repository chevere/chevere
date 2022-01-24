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

namespace Chevere\VarDump\Interfaces;

use Chevere\Throwable\Exceptions\InvalidArgumentException;

/**
 * Describes the component in charge of processing a variable of a known type.
 */
interface VarDumpProcessorInterface
{
    public const MAX_DEPTH = 10;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(VarDumperInterface $varDumper);

    /**
     * Provides the current processor depth.
     */
    public function depth(): int;

    /**
     * Provides the variable type (primitive).
     */
    public function type(): string;

    /**
     * Provides info about the variable like `size=1`, `length=6`, 'Object #id'.
     */
    public function info(): string;

    /**
     * Provides a highlighted type.
     */
    public function typeHighlighted(): string;

    /**
     * Highlights the given operator `$string`.
     */
    public function highlightOperator(string $string): string;

    /**
     * Highlights and wraps in parentheses the given `$string`.
     */
    public function highlightParentheses(string $string): string;

    /**
     * Provides the `*circular reference*` flag.
     */
    public function circularReference(): string;

    /**
     * Provides the `*max depth reached*` flag.
     */
    public function maxDepthReached(): string;

    /**
     * Write the dump to the stream.
     */
    public function write(): void;
}
