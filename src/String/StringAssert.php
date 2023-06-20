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

namespace Chevere\String;

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
use Chevere\String\Interfaces\StringAssertInterface;
use function Chevere\Message\message;

final class StringAssert implements StringAssertInterface
{
    public function __construct(
        private string $string
    ) {
    }

    public function empty(): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isEmpty()) {
            return $this;
        }

        throw new NotEmptyException(
            message('String %string% is not empty')
                ->withCode('%string%', $this->string)
        );
    }

    public function notEmpty(): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isEmpty()) {
            throw new EmptyException(
                message('String is empty')
            );
        }

        return $this;
    }

    public function ctypeSpace(): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isCtypeSpace()) {
            return $this;
        }

        throw new NotCtypeSpaceException(
            message('String %string% is not %algo%')
                ->withCode('%string%', $this->string)
                ->withStrong('%algo%', 'ctype space')
        );
    }

    public function notCtypeSpace(): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isCtypeSpace()) {
            throw new CtypeSpaceException(
                message('String %algo% provided')
                    ->withStrong('%algo%', 'ctype space')
            );
        }

        return $this;
    }

    public function ctypeDigit(): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isCtypeSpace()) {
            return $this;
        }

        throw new NotCtypeDigitException(
            message('String %string% is not %algo%')
                ->withCode('%string%', $this->string)
                ->withStrong('%algo%', 'ctype digit')
        );
    }

    public function notCtypeDigit(): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isCtypeDigit()) {
            throw new CtypeDigitException(
                message('String %algo% provided')
                    ->withStrong('%algo%', 'ctype digit')
            );
        }

        return $this;
    }

    public function startsWithCtypeDigit(): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isStartingWithCtypeDigit()) {
            return $this;
        }

        throw new NotStartsWithCtypeDigitException(
            message('String %string% does not starts with a %algo% character')
                ->withCode('%string%', $this->string)
                ->withStrong('%algo%', 'ctype digit')
        );
    }

    public function notStartsWithCtypeDigit(): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isStartingWithCtypeDigit()) {
            throw new StartsWithCtypeDigitException(
                message('String %string% starts with a %algo% character')
                    ->withCode('%string%', $this->string)
                    ->withStrong('%algo%', 'ctype digit')
            );
        }

        return $this;
    }

    public function startsWith(string $needle): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isStartingWith($needle)) {
            return $this;
        }

        throw new NotStartsWithException(
            message('String %string% does not starts with %needle%')
                ->withCode('%string%', $this->string)
                ->withCode('%needle%', $needle)
        );
    }

    public function notStartsWith(string $needle): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isStartingWith($needle)) {
            throw new StartsWithException(
                message('String %string% starts with %needle%')
                    ->withCode('%string%', $this->string)
                    ->withCode('%needle%', $needle)
            );
        }

        return $this;
    }

    public function endsWith(string $needle): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isEndingWith($needle)) {
            return $this;
        }

        throw new NotEndsWithException(
            message('String %string% does not ends with %needle%')
                ->withCode('%string%', $this->string)
                ->withCode('%needle%', $needle)
        );
    }

    public function notEndsWith(string $needle): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isEndingWith($needle)) {
            throw new EndsWithException(
                message('String %string% ends with %needle%')
                    ->withCode('%string%', $this->string)
                    ->withCode('%needle%', $needle)
            );
        }

        return $this;
    }

    public function same(string $string): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isSame($string)) {
            return $this;
        }

        throw new NotSameException(
            message('Provided string %provided% is not the same as %string%')
                ->withCode('%provided%', $string)
                ->withCode('%string%', $this->string)
        );
    }

    public function notSame(string $string): StringAssertInterface
    {
        if ((new StringValidate($this->string))->isSame($string)) {
            throw new SameException(
                message('Provided string %provided% is the same as %string%')
                    ->withCode('%provided%', $string)
                    ->withCode('%string%', $this->string)
            );
        }

        return $this;
    }

    public function contains(string $string): StringAssertInterface
    {
        if ((new StringValidate($this->string))->contains($string)) {
            return $this;
        }

        throw new NotContainsException(
            message('String %string% not contains %provided%')
                ->withCode('%provided%', $string)
                ->withCode('%string%', $this->string)
        );
    }

    public function notContains(string $string): StringAssertInterface
    {
        if ((new StringValidate($this->string))->contains($string)) {
            throw new ContainsException(
                message('String %string% contains %provided%')
                    ->withCode('%provided%', $string)
                    ->withCode('%string%', $this->string)
            );
        }

        return $this;
    }
}
