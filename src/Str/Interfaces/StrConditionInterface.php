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

namespace Chevere\Str\Interfaces;

/**
 * Describes the component in charge of string conditionals.
 */
interface StrConditionInterface
{
    public function __construct(string $string);

    /**
     * Indicates whether the string is empty.
     */
    public function isEmpty(): bool;

    /**
     * Indicates whether the string is ctype space.
     */
    public function isCtypeSpace(): bool;

    /**
     * Indicates whether the string is ctype digit.
     */
    public function isCtypeDigit(): bool;

    /**
     * Indicates whether the string starts with ctype digit.
     */
    public function isStartingWithCtypeDigit(): bool;

    /**
     * Indicates whether the string starts with `$needle`.
     */
    public function isStartingWith(string $needle): bool;

    /**
     * Indicates whether the string ends with `$needle`.
     */
    public function isEndingWith(string $needle): bool;

    /**
     * Indicates whether the string is the same as `$needle`.
     */
    public function isSame(string $string): bool;
}
