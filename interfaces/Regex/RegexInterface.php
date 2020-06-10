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

use Chevere\Interfaces\To\ToStringInterface;

interface RegexInterface extends ToStringInterface
{
    const ERRORS = [
        PREG_NO_ERROR => 'PREG_NO_ERROR', // duh!
        PREG_INTERNAL_ERROR => 'PREG_INTERNAL_ERROR',
        PREG_BACKTRACK_LIMIT_ERROR => 'PREG_BACKTRACK_LIMIT_ERROR',
        PREG_RECURSION_LIMIT_ERROR => 'PREG_RECURSION_LIMIT_ERROR',
        PREG_BAD_UTF8_ERROR => 'PREG_BAD_UTF8_ERROR',
        PREG_BAD_UTF8_OFFSET_ERROR => 'PREG_BAD_UTF8_OFFSET_ERROR',
        PREG_JIT_STACKLIMIT_ERROR => 'PREG_JIT_STACKLIMIT_ERROR',
    ];

    public function __construct(string $string);

    public function assertNoCapture(): void;

    /**
     * @return string The regex string used to create the object
     */
    public function toString(): string;

    /**
     * @return string The regex string without delimiter char
     */
    public function toNoDelimiters(): string;

    /**
     * @return string The regex string without delimiter char, without anchors (^, $)
     */
    public function toNoDelimitersNoAnchors(): string;
}
