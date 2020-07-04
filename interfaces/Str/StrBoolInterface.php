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

/**
 * Describes the component in charge of providing string conditionals.
 */
interface StrBoolInterface
{
    public function __construct(string $string);

    /**
     * Indicates whether the string is empty.
     */
    public function empty(): bool;

    /**
     * Indicates whether the string is ctype space.
     */
    public function ctypeSpace(): bool;

    /**
     * Indicates whether the string is ctype digit.
     */
    public function ctypeDigit(): bool;

    /**
     * Indicates whether the string starts with ctype digit.
     */
    public function startsWithCtypeDigit(): bool;

    /**
     * Indicates whether the string starts with `$needle`.
     */
    public function startsWith(string $needle): bool;

    /**
     * Indicates whether the string ends with `$needle`.
     */
    public function endsWith(string $needle): bool;

    /**
     * Indicates whether the string is the same as `$needle`.
     */
    public function same(string $string): bool;
}
