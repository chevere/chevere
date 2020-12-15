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

namespace Chevere\Components\Str;

use Chevere\Components\Message\Message;
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
use Chevere\Interfaces\Str\StrAssertInterface;

final class StrAssert implements StrAssertInterface
{
    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function empty(): StrAssertInterface
    {
        if ((new StrBool($this->string))->empty()) {
            return $this;
        }

        throw new StrNotEmptyException(
            (new Message('String %string% is not empty'))
                ->code('%string%', $this->string)
        );
    }

    public function notEmpty(): StrAssertInterface
    {
        if ((new StrBool($this->string))->empty()) {
            throw new StrEmptyException(
                (new Message('String is empty'))
            );
        }

        return $this;
    }

    public function ctypeSpace(): StrAssertInterface
    {
        if ((new StrBool($this->string))->ctypeSpace()) {
            return $this;
        }

        throw new StrNotCtypeSpaceException(
            (new Message('String %string% is not %algo%'))
                ->code('%string%', $this->string)
                ->strong('%algo%', 'ctype space')
        );
    }

    public function notCtypeSpace(): StrAssertInterface
    {
        if ((new StrBool($this->string))->ctypeSpace()) {
            throw new StrCtypeSpaceException(
                (new Message('String %algo% provided'))
                    ->strong('%algo%', 'ctype space')
            );
        }

        return $this;
    }

    public function ctypeDigit(): StrAssertInterface
    {
        if ((new StrBool($this->string))->ctypeSpace()) {
            return $this;
        }

        throw new StrNotCtypeDigitException(
            (new Message('String %string% is not %algo%'))
                ->code('%string%', $this->string)
                ->strong('%algo%', 'ctype digit')
        );
    }

    public function notCtypeDigit(): StrAssertInterface
    {
        if ((new StrBool($this->string))->ctypeDigit()) {
            throw new StrCtypeDigitException(
                (new Message('String %algo% provided'))
                    ->strong('%algo%', 'ctype digit')
            );
        }

        return $this;
    }

    public function startsWithCtypeDigit(): StrAssertInterface
    {
        if ((new StrBool($this->string))->startsWithCtypeDigit()) {
            return $this;
        }

        throw new StrNotStartsWithCtypeDigitException(
            (new Message('String %string% does not starts with a %algo% character'))
                ->code('%string%', $this->string)
                ->strong('%algo%', 'ctype digit')
        );
    }

    public function notStartsWithCtypeDigit(): StrAssertInterface
    {
        if ((new StrBool($this->string))->startsWithCtypeDigit()) {
            throw new StrStartsWithCtypeDigitException(
                (new Message('String %string% starts with a %algo% character'))
                    ->code('%string%', $this->string)
                    ->strong('%algo%', 'ctype digit')
            );
        }

        return $this;
    }

    public function startsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->startsWith($needle)) {
            return $this;
        }

        throw new StrNotStartsWithException(
            (new Message('String %string% does not starts with %needle%'))
                ->code('%string%', $this->string)
                ->code('%needle%', $needle)
        );
    }

    public function notStartsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->startsWith($needle)) {
            throw new StrStartsWithException(
                (new Message('String %string% starts with %needle%'))
                    ->code('%string%', $this->string)
                    ->code('%needle%', $needle)
            );
        }

        return $this;
    }

    public function endsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->endsWith($needle)) {
            return $this;
        }

        throw new StrNotEndsWithException(
            (new Message('String %string% does not ends with %needle%'))
                ->code('%string%', $this->string)
                ->code('%needle%', $needle)
        );
    }

    public function notEndsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->endsWith($needle)) {
            throw new StrEndsWithException(
                (new Message('String %string% ends with %needle%'))
                    ->code('%string%', $this->string)
                    ->code('%needle%', $needle)
            );
        }

        return $this;
    }

    public function same(string $string): StrAssertInterface
    {
        if ((new StrBool($this->string))->same($string)) {
            return $this;
        }

        throw new StrNotSameException(
            (new Message('Provided string %provided% is not the same as %string%'))
                ->code('%provided%', $string)
                ->code('%string%', $this->string)
        );
    }

    public function notSame(string $string): StrAssertInterface
    {
        if ((new StrBool($this->string))->same($string)) {
            throw new StrSameException(
                (new Message('Provided string %provided% is the same as %string%'))
                    ->code('%provided%', $string)
                    ->code('%string%', $this->string)
            );
        }

        return $this;
    }

    public function contains(string $string): StrAssertInterface
    {
        if ((new StrBool($this->string))->contains($string)) {
            return $this;
        }

        throw new StrNotContainsException(
            (new Message('String %string% not contains %provided%'))
                ->code('%provided%', $string)
                ->code('%string%', $this->string)
        );
    }

    public function notContains(string $string): StrAssertInterface
    {
        if ((new StrBool($this->string))->contains($string)) {
            throw new StrContainsException(
                (new Message('String %string% contains %provided%'))
                    ->code('%provided%', $string)
                    ->code('%string%', $this->string)
            );
        }

        return $this;
    }
}
