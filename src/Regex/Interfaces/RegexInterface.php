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

use Chevere\Regex\Exceptions\NoMatchException;
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
     * Provides access to the the regex string without delimiters.
     */
    public function noDelimiters(): string;

    /**
     * Provides access to the regex string without delimiters and anchors.
     */
    public function noDelimitersNoAnchors(): string;

    /**
     * @return array<int, string>
     * @throws RuntimeException
     */
    public function match(string $string): array;

    /**
     * @return array<array<int, string>>
     * @throws RuntimeException
     */
    public function matchAll(string $string): array;

    /**
     * @throws NoMatchException
     * @throws RuntimeException
     */
    public function assertMatch(string $string): void;

    /**
     * @throws NoMatchException
     * @throws RuntimeException
     */
    public function assertMatchAll(string $string): void;
}
