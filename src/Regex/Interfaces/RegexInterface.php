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

namespace Chevere\Regex\Interfaces;

use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\RuntimeException;
use Stringable;

/**
 * Describes the component in charge of interacting with PCRE - Perl Compatible Regular Expressions.
 */
interface RegexInterface extends Stringable
{
    public const ERRORS = [
        PREG_NO_ERROR => 'PREG_NO_ERROR',
        PREG_INTERNAL_ERROR => 'PREG_INTERNAL_ERROR',
        PREG_BACKTRACK_LIMIT_ERROR => 'PREG_BACKTRACK_LIMIT_ERROR',
        PREG_RECURSION_LIMIT_ERROR => 'PREG_RECURSION_LIMIT_ERROR',
        PREG_BAD_UTF8_ERROR => 'PREG_BAD_UTF8_ERROR',
        PREG_BAD_UTF8_OFFSET_ERROR => 'PREG_BAD_UTF8_OFFSET_ERROR',
        PREG_JIT_STACKLIMIT_ERROR => 'PREG_JIT_STACKLIMIT_ERROR',
    ];

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $pattern);

    /**
     * Provides access to the the regex string without delimiters.
     */
    public function toNoDelimiters(): string;

    /**
     * Provides access to the regex string without delimiters and without anchors (`^`, `$`).
     */
    public function toNoDelimitersNoAnchors(): string;

    /**
     * Matches string.
     *
     * @return array<int, string>
     *
     * @throws RuntimeException
     */
    public function match(string $string): array;

    /**
     * Matches all strings.
     *
     * @return array<array<int, string>>
     *
     * @throws RuntimeException
     */
    public function matchAll(string $string): array;
}
