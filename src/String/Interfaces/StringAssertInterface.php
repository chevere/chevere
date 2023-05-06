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

use Chevere\String\Exceptions\ContainsException;
use Chevere\String\Exceptions\CtypeDigitException;
use Chevere\String\Exceptions\CtypeSpaceException;
use Chevere\String\Exceptions\EmptyException;
use Chevere\String\Exceptions\EndsWithException;
use Chevere\String\Exceptions\NotContainsException;
use Chevere\String\Exceptions\NotCtypeDigitException;
use Chevere\String\Exceptions\NotCtypeSpaceException;
use Chevere\String\Exceptions\NotEmptyException;
use Chevere\String\Exceptions\NotEndsWithException;
use Chevere\String\Exceptions\NotSameException;
use Chevere\String\Exceptions\NotStartsWithCtypeDigitException;
use Chevere\String\Exceptions\NotStartsWithException;
use Chevere\String\Exceptions\SameException;
use Chevere\String\Exceptions\StartsWithCtypeDigitException;
use Chevere\String\Exceptions\StartsWithException;

/**
 * Describes the component in charge of string assertions.
 */
interface StringAssertInterface
{
    /**
     * Asserts that the string is empty.
     *
     * @throws NotEmptyException
     */
    public function empty(): self;

    /**
     * Asserts that the string is not empty.
     *
     * @throws EmptyException
     */
    public function notEmpty(): self;

    /**
     * Asserts that the string is ctype space.
     *
     * @throws NotCtypeSpaceException
     */
    public function ctypeSpace(): self;

    /**
     * Asserts that the string is not ctype space.
     *
     * @throws CtypeSpaceException
     */
    public function notCtypeSpace(): self;

    /**
     * Asserts that the string is ctype digit.
     *
     * @throws NotCtypeDigitException
     */
    public function ctypeDigit(): self;

    /**
     * Asserts that the string is not ctype digit.
     *
     * @throws CtypeDigitException
     */
    public function notCtypeDigit(): self;

    /**
     * Asserts that the string is starts with ctype digit.
     *
     * @throws NotStartsWithCtypeDigitException
     */
    public function startsWithCtypeDigit(): self;

    /**
     * Asserts that the string not starts with ctype digit.
     *
     * @throws StartsWithCtypeDigitException
     */
    public function notStartsWithCtypeDigit(): self;

    /**
     * Asserts that the string is starts with `$needle`.
     *
     * @throws NotStartsWithException
     */
    public function startsWith(string $needle): self;

    /**
     * Asserts that the string not starts with `$needle`.
     *
     * @throws StartsWithException
     */
    public function notStartsWith(string $needle): self;

    /**
     * Asserts that the string ends with `$needle`.
     *
     * @throws NotEndsWithException
     */
    public function endsWith(string $needle): self;

    /**
     * Asserts that the string not ends with `$needle`.
     *
     * @throws EndsWithException
     */
    public function notEndsWith(string $needle): self;

    /**
     * Asserts that the string is same as `$string`.
     *
     * @throws NotSameException
     */
    public function same(string $string): self;

    /**
     * Asserts that the string is not same as `$string`.
     *
     * @throws SameException
     */
    public function notSame(string $string): self;

    /**
     * Asserts that the string contains `$string`.
     *
     * @throws NotContainsException
     */
    public function contains(string $string): self;

    /**
     * Asserts that the string not contains `$string`.
     *
     * @throws ContainsException
     */
    public function notContains(string $string): self;
}
