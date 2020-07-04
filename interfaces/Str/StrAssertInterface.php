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

use Chevere\Exceptions\Str\StrContainsException;
use Chevere\Exceptions\Str\StrCtypeDigitException;
use Chevere\Exceptions\Str\StrCtypeSpaceException;
use Chevere\Exceptions\Str\StrEmptyException;
use Chevere\Exceptions\Str\StrEndsWithException;
use Chevere\Exceptions\Str\StrNotContainsException;
use Chevere\Exceptions\Str\StrNotCtypeDigitException;
use Chevere\Exceptions\Str\StrNotCtypeSpaceException;
use Chevere\Exceptions\Str\StrNotEmptyException;
use Chevere\Exceptions\Str\StrNotEndsWithException;
use Chevere\Exceptions\Str\StrNotSameException;
use Chevere\Exceptions\Str\StrNotStartsWithCtypeDigitException;
use Chevere\Exceptions\Str\StrNotStartsWithException;
use Chevere\Exceptions\Str\StrSameException;
use Chevere\Exceptions\Str\StrStartsWithCtypeDigitException;
use Chevere\Exceptions\Str\StrStartsWithException;

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
    public function empty(): StrAssertInterface;

    /**
     * Asserts that the string is not empty.
     *
     * @throws StrEmptyException
     */
    public function notEmpty(): StrAssertInterface;

    /**
     * Asserts that the string is ctype space.
     *
     * @throws StrNotCtypeSpaceException
     */
    public function ctypeSpace(): StrAssertInterface;

    /**
     * Asserts that the string is not ctype space.
     *
     * @throws StrCtypeSpaceException
     */
    public function notCtypeSpace(): StrAssertInterface;

    /**
     * Asserts that the string is ctype digit.
     *
     * @throws StrNotCtypeDigitException
     */
    public function ctypeDigit(): StrAssertInterface;

    /**
     * Asserts that the string is not ctype digit.
     *
     * @throws StrCtypeDigitException
     */
    public function notCtypeDigit(): StrAssertInterface;

    /**
     * Asserts that the string is starts with ctype digit.
     *
     * @throws StrNotStartsWithCtypeDigitException
     */
    public function startsWithCtypeDigit(): StrAssertInterface;

    /**
     * Asserts that the string not starts with ctype digit.
     *
     * @throws StrStartsWithCtypeDigitException
     */
    public function notStartsWithCtypeDigit(): StrAssertInterface;

    /**
     * Asserts that the string is starts with `$needle`.
     *
     * @throws StrNotStartsWithException
     */
    public function startsWith(string $needle): StrAssertInterface;

    /**
     * Asserts that the string not starts with `$needle`.
     *
     * @throws StrStartsWithException
     */
    public function notStartsWith(string $needle): StrAssertInterface;

    /**
     * Asserts that the string ends with `$needle`.
     *
     * @throws StrNotEndsWithException
     */
    public function endsWith(string $needle): StrAssertInterface;

    /**
     * Asserts that the string not ends with `$needle`.
     *
     * @throws StrEndsWithException
     */
    public function notEndsWith(string $needle): StrAssertInterface;

    /**
     * Asserts that the string is same as `$string`.
     *
     * @throws StrNotSameException
     */
    public function same(string $string): StrAssertInterface;

    /**
     * Asserts that the string is not same as `$string`.
     *
     * @throws StrSameException
     */
    public function notSame(string $string): StrAssertInterface;

    /**
     * Asserts that the string contains `$string`.
     *
     * @throws StrNotContainsException
     */
    public function contains(string $string): StrAssertInterface;

    /**
     * Asserts that the string not contains `$string`.
     *
     * @throws StrContainsException
     */
    public function notContains(string $string): StrAssertInterface;
}
