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

namespace Chevere\Str;

use function Chevere\Message\message;
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
use Chevere\Str\Interfaces\StrAssertInterface;

final class StrAssert implements StrAssertInterface
{
    public function __construct(
        private string $string
    ) {
    }

    public function empty(): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isEmpty()) {
            return $this;
        }

        throw new StrNotEmptyException(
            message('String %string% is not empty')
                ->withCode('%string%', $this->string)
        );
    }

    public function notEmpty(): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isEmpty()) {
            throw new StrEmptyException(
                message('String is empty')
            );
        }

        return $this;
    }

    public function ctypeSpace(): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isCtypeSpace()) {
            return $this;
        }

        throw new StrNotCtypeSpaceException(
            message('String %string% is not %algo%')
                ->withCode('%string%', $this->string)
                ->withStrong('%algo%', 'ctype space')
        );
    }

    public function notCtypeSpace(): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isCtypeSpace()) {
            throw new StrCtypeSpaceException(
                message('String %algo% provided')
                    ->withStrong('%algo%', 'ctype space')
            );
        }

        return $this;
    }

    public function ctypeDigit(): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isCtypeSpace()) {
            return $this;
        }

        throw new StrNotCtypeDigitException(
            message('String %string% is not %algo%')
                ->withCode('%string%', $this->string)
                ->withStrong('%algo%', 'ctype digit')
        );
    }

    public function notCtypeDigit(): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isCtypeDigit()) {
            throw new StrCtypeDigitException(
                message('String %algo% provided')
                    ->withStrong('%algo%', 'ctype digit')
            );
        }

        return $this;
    }

    public function startsWithCtypeDigit(): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isStartingWithCtypeDigit()) {
            return $this;
        }

        throw new StrNotStartsWithCtypeDigitException(
            message('String %string% does not starts with a %algo% character')
                ->withCode('%string%', $this->string)
                ->withStrong('%algo%', 'ctype digit')
        );
    }

    public function notStartsWithCtypeDigit(): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isStartingWithCtypeDigit()) {
            throw new StrStartsWithCtypeDigitException(
                message('String %string% starts with a %algo% character')
                    ->withCode('%string%', $this->string)
                    ->withStrong('%algo%', 'ctype digit')
            );
        }

        return $this;
    }

    public function startsWith(string $needle): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isStartingWith($needle)) {
            return $this;
        }

        throw new StrNotStartsWithException(
            message('String %string% does not starts with %needle%')
                ->withCode('%string%', $this->string)
                ->withCode('%needle%', $needle)
        );
    }

    public function notStartsWith(string $needle): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isStartingWith($needle)) {
            throw new StrStartsWithException(
                message('String %string% starts with %needle%')
                    ->withCode('%string%', $this->string)
                    ->withCode('%needle%', $needle)
            );
        }

        return $this;
    }

    public function endsWith(string $needle): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isEndingWith($needle)) {
            return $this;
        }

        throw new StrNotEndsWithException(
            message('String %string% does not ends with %needle%')
                ->withCode('%string%', $this->string)
                ->withCode('%needle%', $needle)
        );
    }

    public function notEndsWith(string $needle): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isEndingWith($needle)) {
            throw new StrEndsWithException(
                message('String %string% ends with %needle%')
                    ->withCode('%string%', $this->string)
                    ->withCode('%needle%', $needle)
            );
        }

        return $this;
    }

    public function same(string $string): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isSame($string)) {
            return $this;
        }

        throw new StrNotSameException(
            message('Provided string %provided% is not the same as %string%')
                ->withCode('%provided%', $string)
                ->withCode('%string%', $this->string)
        );
    }

    public function notSame(string $string): StrAssertInterface
    {
        if ((new StrCondition($this->string))->isSame($string)) {
            throw new StrSameException(
                message('Provided string %provided% is the same as %string%')
                    ->withCode('%provided%', $string)
                    ->withCode('%string%', $this->string)
            );
        }

        return $this;
    }

    public function contains(string $string): StrAssertInterface
    {
        if ((new StrCondition($this->string))->contains($string)) {
            return $this;
        }

        throw new StrNotContainsException(
            message('String %string% not contains %provided%')
                ->withCode('%provided%', $string)
                ->withCode('%string%', $this->string)
        );
    }

    public function notContains(string $string): StrAssertInterface
    {
        if ((new StrCondition($this->string))->contains($string)) {
            throw new StrContainsException(
                message('String %string% contains %provided%')
                    ->withCode('%provided%', $string)
                    ->withCode('%string%', $this->string)
            );
        }

        return $this;
    }
}
