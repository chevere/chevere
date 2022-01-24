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

use Chevere\Str\Exceptions\StrContainsException;
use Chevere\Str\Exceptions\StrCtypeDigitException;
use Chevere\Str\Exceptions\StrCtypeSpaceException;
use Chevere\Str\Exceptions\StrEmptyException;
use Chevere\Str\Exceptions\StrEndsWithException;
use Chevere\Str\Exceptions\StrNotContainsException;
use Chevere\Str\Exceptions\StrNotCtypeDigitException;
use Chevere\Str\Exceptions\StrNotCtypeSpaceException;
use Chevere\Str\Exceptions\StrNotEmptyException;
use Chevere\Str\Exceptions\StrNotEndsWithException;
use Chevere\Str\Exceptions\StrNotSameException;
use Chevere\Str\Exceptions\StrNotStartsWithCtypeDigitException;
use Chevere\Str\Exceptions\StrNotStartsWithException;
use Chevere\Str\Exceptions\StrSameException;
use Chevere\Str\Exceptions\StrStartsWithCtypeDigitException;
use Chevere\Str\Exceptions\StrStartsWithException;

/**
 * Describes the component in charge of string asserting.
 */
interface StrAssertInterface
{
    public function __construct(string $string);

    /**
     * Asserts that the string is empty.
     *
     * @throws StrNotEmptyException
     */
    public function empty(): self;

    /**
     * Asserts that the string is not empty.
     *
     * @throws StrEmptyException
     */
    public function notEmpty(): self;

    /**
     * Asserts that the string is ctype space.
     *
     * @throws StrNotCtypeSpaceException
     */
    public function ctypeSpace(): self;

    /**
     * Asserts that the string is not ctype space.
     *
     * @throws StrCtypeSpaceException
     */
    public function notCtypeSpace(): self;

    /**
     * Asserts that the string is ctype digit.
     *
     * @throws StrNotCtypeDigitException
     */
    public function ctypeDigit(): self;

    /**
     * Asserts that the string is not ctype digit.
     *
     * @throws StrCtypeDigitException
     */
    public function notCtypeDigit(): self;

    /**
     * Asserts that the string is starts with ctype digit.
     *
     * @throws StrNotStartsWithCtypeDigitException
     */
    public function startsWithCtypeDigit(): self;

    /**
     * Asserts that the string not starts with ctype digit.
     *
     * @throws StrStartsWithCtypeDigitException
     */
    public function notStartsWithCtypeDigit(): self;

    /**
     * Asserts that the string is starts with `$needle`.
     *
     * @throws StrNotStartsWithException
     */
    public function startsWith(string $needle): self;

    /**
     * Asserts that the string not starts with `$needle`.
     *
     * @throws StrStartsWithException
     */
    public function notStartsWith(string $needle): self;

    /**
     * Asserts that the string ends with `$needle`.
     *
     * @throws StrNotEndsWithException
     */
    public function endsWith(string $needle): self;

    /**
     * Asserts that the string not ends with `$needle`.
     *
     * @throws StrEndsWithException
     */
    public function notEndsWith(string $needle): self;

    /**
     * Asserts that the string is same as `$string`.
     *
     * @throws StrNotSameException
     */
    public function same(string $string): self;

    /**
     * Asserts that the string is not same as `$string`.
     *
     * @throws StrSameException
     */
    public function notSame(string $string): self;

    /**
     * Asserts that the string contains `$string`.
     *
     * @throws StrNotContainsException
     */
    public function contains(string $string): self;

    /**
     * Asserts that the string not contains `$string`.
     *
     * @throws StrContainsException
     */
    public function notContains(string $string): self;
}
