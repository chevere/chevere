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

namespace Chevere\Interfaces\Str;

interface StrInterface
{
    public function __construct(string $string);

    public function toString(): string;

    /**
     * Lowercase string UTF-8
     */
    public function lowercase(): StrInterface;

    /**
     * Uppercase string UTF-8
     */
    public function uppercase(): StrInterface;

    /**
     * Strip whitespaces from string.
     */
    public function stripWhitespace(): StrInterface;

    /**
     * Strip extra whitespace from string.
     */
    public function stripExtraWhitespace(): StrInterface;

    /**
     * Strip non-alphanumeric chars from string.
     */
    public function stripNonAlphanumerics(): StrInterface;

    /**
     * Converts backslash into forward slashes.
     */
    public function forwardSlashes(): StrInterface;

    /**
     * Prepends a string with a tail string, without repeats.
     *
     * @param string $tail string tail
     */
    public function leftTail(string $tail): StrInterface;

    /**
     * Appends a tail to string, without repeats.
     *
     * @param string $tail string tail
     */
    public function rightTail(string $tail): StrInterface;

    /**
     * Replace the first occurrence of the search string with the replacement
     * string.
     *
     * @param string $search  value being searched for
     * @param string $replace replacement value that replaces found search values
     */
    public function replaceFirst(string $search, string $replace): StrInterface;

    /**
     * Replace the last occurrence of the search string with the replacement string.
     *
     * @param string $search  value being searched for
     * @param string $replace replacement value that replaces found search values
     */
    public function replaceLast(string $search, string $replace): StrInterface;

    /**
     * Removes CLI color format.
     */
    public function stripANSIColors(): StrInterface;
}
