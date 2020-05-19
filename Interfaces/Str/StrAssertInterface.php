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

interface StrAssertInterface
{
    public function __construct(string $string);

    /**
     * @throws StrNotEmptyException
     */
    public function empty(): StrAssertInterface;

    /**
     * @throws StrEmptyException
     */
    public function notEmpty(): StrAssertInterface;

    /**
     * @throws StrNotCtypeSpaceException
     */
    public function ctypeSpace(): StrAssertInterface;

    /**
     * @throws StrCtypeSpaceException
     */
    public function notCtypeSpace(): StrAssertInterface;

    /**
     * @throws StrNotCtypeDigitException
     */
    public function ctypeDigit(): StrAssertInterface;

    /**
     * @throws StrCtypeDigitException
     */
    public function notCtypeDigit(): StrAssertInterface;

    /**
     * @throws StrNotStartsWithCtypeDigitException
     */
    public function startsWithCtypeDigit(): StrAssertInterface;

    /**
     * @throws StrStartsWithCtypeDigitException
     */
    public function notStartsWithCtypeDigit(): StrAssertInterface;

    /**
     * @throws StrNotStartsWithException
     */
    public function startsWith(string $needle): StrAssertInterface;

    /**
     * @throws StrStartsWithException
     */
    public function notStartsWith(string $needle): StrAssertInterface;

    /**
     * @throws StrNotEndsWithException
     */
    public function endsWith(string $needle): StrAssertInterface;

    /**
     * @throws StrEndsWithException
     */
    public function notEndsWith(string $needle): StrAssertInterface;

    /**
     * @throws StrNotSameException
     */
    public function same(string $string): StrAssertInterface;

    /**
     * @throws StrSameException
     */
    public function notSame(string $string): StrAssertInterface;

    /**
     * @throws StrNotContainsException
     */
    public function contains(string $string): StrAssertInterface;

    /**
     * @throws StrContainsException
     */
    public function notContains(string $string): StrAssertInterface;
}
