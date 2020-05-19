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

use Chevere\Components\Str\Exceptions\StrContainsException;
use Chevere\Components\Str\Exceptions\StrCtypeDigitException;
use Chevere\Components\Str\Exceptions\StrCtypeSpaceException;
use Chevere\Components\Str\Exceptions\StrEmptyException;
use Chevere\Components\Str\Exceptions\StrEndsWithException;
use Chevere\Components\Str\Exceptions\StrNotContainsException;
use Chevere\Components\Str\Exceptions\StrNotCtypeDigitException;
use Chevere\Components\Str\Exceptions\StrNotCtypeSpaceException;
use Chevere\Components\Str\Exceptions\StrNotEmptyException;
use Chevere\Components\Str\Exceptions\StrNotEndsWithException;
use Chevere\Components\Str\Exceptions\StrNotSameException;
use Chevere\Components\Str\Exceptions\StrNotStartsWithCtypeDigitException;
use Chevere\Components\Str\Exceptions\StrNotStartsWithException;
use Chevere\Components\Str\Exceptions\StrSameException;
use Chevere\Components\Str\Exceptions\StrStartsWithCtypeDigitException;
use Chevere\Components\Str\Exceptions\StrStartsWithException;

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
