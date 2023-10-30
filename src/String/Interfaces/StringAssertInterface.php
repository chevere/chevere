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

namespace Chevere\String\Interfaces;

/**
 * Describes the component in charge of string assertions.
 */
interface StringAssertInterface
{
    /**
     * Asserts that the string is empty.
     */
    public function empty(): self;

    /**
     * Asserts that the string is not empty.
     */
    public function notEmpty(): self;

    /**
     * Asserts that the string is ctype space.
     */
    public function ctypeSpace(): self;

    /**
     * Asserts that the string is not ctype space.
     */
    public function notCtypeSpace(): self;

    /**
     * Asserts that the string is ctype digit.
     */
    public function ctypeDigit(): self;

    /**
     * Asserts that the string is not ctype digit.
     */
    public function notCtypeDigit(): self;

    /**
     * Asserts that the string is starts with ctype digit.
     */
    public function startsWithCtypeDigit(): self;

    /**
     * Asserts that the string not starts with ctype digit.
     */
    public function notStartsWithCtypeDigit(): self;

    /**
     * Asserts that the string is starts with `$needle`.
     */
    public function startsWith(string $needle): self;

    /**
     * Asserts that the string not starts with `$needle`.
     */
    public function notStartsWith(string $needle): self;

    /**
     * Asserts that the string ends with `$needle`.
     */
    public function endsWith(string $needle): self;

    /**
     * Asserts that the string not ends with `$needle`.
     */
    public function notEndsWith(string $needle): self;

    /**
     * Asserts that the string is same as `$string`.
     */
    public function same(string $string): self;

    /**
     * Asserts that the string is not same as `$string`.
     */
    public function notSame(string $string): self;

    /**
     * Asserts that the string contains `$string`.
     */
    public function contains(string $string): self;

    /**
     * Asserts that the string not contains `$string`.
     */
    public function notContains(string $string): self;
}
