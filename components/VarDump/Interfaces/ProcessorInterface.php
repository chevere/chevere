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

interface ProcessorInterface
{
    const MAX_DEPTH = 5;

    /**
     * Provides access to the instance info.
     * The information about the variable like `size=1` or `length=6`
     */
    public function info(): string;

    public function typeHighlighted(): string;

    public function highlightOperator(string $string): string;

    public function highlightParentheses(string $string): string;

    public function circularReference(): string;

    public function maxDepthReached(): string;

    /**
     * Provides access to the instance type.
     * The information about the variable type like `array` or `object`
     */
    public function type(): string;

    public function write(): void;
}
