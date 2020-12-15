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

namespace Chevere\Interfaces\Regex;

use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Regex\RegexInvalidException;
use Chevere\Interfaces\To\ToStringInterface;

/**
 * Describes the component in charge of interacting with PCRE - Perl Compatible Regular Expressions.
 */
interface RegexInterface extends ToStringInterface
{
    public const ERRORS = [
        PREG_NO_ERROR => 'PREG_NO_ERROR', // duh!
        PREG_INTERNAL_ERROR => 'PREG_INTERNAL_ERROR',
        PREG_BACKTRACK_LIMIT_ERROR => 'PREG_BACKTRACK_LIMIT_ERROR',
        PREG_RECURSION_LIMIT_ERROR => 'PREG_RECURSION_LIMIT_ERROR',
        PREG_BAD_UTF8_ERROR => 'PREG_BAD_UTF8_ERROR',
        PREG_BAD_UTF8_OFFSET_ERROR => 'PREG_BAD_UTF8_OFFSET_ERROR',
        PREG_JIT_STACKLIMIT_ERROR => 'PREG_JIT_STACKLIMIT_ERROR',
    ];

    /**
     * @throws RegexInvalidException
     */
    public function __construct(string $string);

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
     * @throws RuntimeException
     */
    public function match(string $string): array;

    /**
     * Matches all string.
     *
     * @throws RuntimeException
     */
    public function matchAll(string $string): array;
}
