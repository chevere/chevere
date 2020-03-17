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
use Chevere\Components\Str\Exceptions\StrContainsException;
use Chevere\Components\Str\Exceptions\StrCtypeSpaceException;
use Chevere\Components\Str\Exceptions\StrEmptyException;
use Chevere\Components\Str\Exceptions\StrEndsWithException;
use Chevere\Components\Str\Exceptions\StrNotContainsException;
use Chevere\Components\Str\Exceptions\StrNotCtypeSpaceException;
use Chevere\Components\Str\Exceptions\StrNotEmptyException;
use Chevere\Components\Str\Exceptions\StrNotEndsWithException;
use Chevere\Components\Str\Exceptions\StrNotSameException;
use Chevere\Components\Str\Exceptions\StrNotStartsWithCtypeDigitException;
use Chevere\Components\Str\Exceptions\StrNotStartsWithException;
use Chevere\Components\Str\Exceptions\StrSameException;
use Chevere\Components\Str\Exceptions\StrStartsWithCtypeDigitException;
use Chevere\Components\Str\Exceptions\StrStartsWithException;
use Chevere\Components\Str\Interfaces\StrAssertInterface;
use Chevere\Components\Str\StrBool;

final class StrAssert implements StrAssertInterface
{
    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * @throws StrNotEmptyException
     */
    public function empty(): StrAssertInterface
    {
        if ((new StrBool($this->string))->empty()) {
            return $this;
        }
        throw new StrNotEmptyException(
            (new Message('String %string% is not empty'))
                ->code('%string%', $this->string)
                ->toString()
        );
    }

    /**
     * @throws StrEmptyException
     */
    public function notEmpty(): StrAssertInterface
    {
        if ((new StrBool($this->string))->empty()) {
            throw new StrEmptyException(
                (new Message('String is empty'))
                    ->toString()
            );
        }

        return $this;
    }

    /**
     * @throws StrNotCtypeSpaceException
     */
    public function ctypeSpace(): StrAssertInterface
    {
        if ((new StrBool($this->string))->ctypeSpace()) {
            return $this;
        }
        throw new StrNotCtypeSpaceException(
            (new Message('String %string% is not %algo%'))
                ->code('%string%', $this->string)
                ->strong('%algo%', 'ctype space')
                ->toString()
        );
    }

    /**
     * @throws StrCtypeSpaceException
     */
    public function notCtypeSpace(): StrAssertInterface
    {
        if ((new StrBool($this->string))->ctypeSpace()) {
            throw new StrCtypeSpaceException(
                (new Message('String %algo% provided'))
                    ->strong('%algo%', 'ctype space')
                    ->toString()
            );
        }

        return $this;
    }

    /**
     * @throws StrNotStartsWithCtypeDigitException
     */
    public function startsWithCtypeDigit(): StrAssertInterface
    {
        if ((new StrBool($this->string))->startsWithCtypeDigit()) {
            return $this;
        }
        throw new StrNotStartsWithCtypeDigitException(
            (new Message('String %string% does not starts with a %algo% character'))
                ->code('%string%', $this->string)
                ->strong('%algo%', 'ctype digit')
                ->toString()
        );
    }

    /**
     * @throws StrStartsWithCtypeDigitException
     */
    public function notStartsWithCtypeDigit(): StrAssertInterface
    {
        if ((new StrBool($this->string))->startsWithCtypeDigit()) {
            throw new StrStartsWithCtypeDigitException(
                (new Message('String %string% starts with a %algo% character'))
                    ->code('%string%', $this->string)
                    ->strong('%algo%', 'ctype digit')
                    ->toString()
            );
        }

        return $this;
    }

    /**
     * @throws StrNotStartsWithException
     */
    public function startsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->startsWith($needle)) {
            return $this;
        }
        throw new StrNotStartsWithException(
            (new Message('String %string% does not starts with %needle%'))
                ->code('%string%', $this->string)
                ->code('%needle%', $needle)
                ->toString()
        );
    }

    /**
     * @throws StrStartsWithException
     */
    public function notStartsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->startsWith($needle)) {
            throw new StrStartsWithException(
                (new Message('String %string% starts with %needle%'))
                    ->code('%string%', $this->string)
                    ->code('%needle%', $needle)
                    ->toString()
            );
        }

        return $this;
    }

    /**
     * @throws StrNotEndsWithException
     */
    public function endsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->endsWith($needle)) {
            return $this;
        }
        throw new StrNotEndsWithException(
            (new Message('String %string% does not ends with %needle%'))
                ->code('%string%', $this->string)
                ->code('%needle%', $needle)
                ->toString()
        );
    }

    /**
     * @throws StrEndsWithException
     */
    public function notEndsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->endsWith($needle)) {
            throw new StrEndsWithException(
                (new Message('String %string% ends with %needle%'))
                    ->code('%string%', $this->string)
                    ->code('%needle%', $needle)
                    ->toString()
            );
        }

        return $this;
    }

    /**
     * @throws StrNotSameException
     */
    public function same(string $string): StrAssertInterface
    {
        if ((new StrBool($this->string))->same($string)) {
            return $this;
        }
        throw new StrNotSameException(
            (new Message('Provided string %provided% is not the same as %string%'))
                ->code('%provided%', $string)
                ->code('%string%', $this->string)
                ->toString()
        );
    }

    /**
     * @throws StrSameException
     */
    public function notSame(string $string): StrAssertInterface
    {
        if ((new StrBool($this->string))->same($string)) {
            throw new StrSameException(
                (new Message('Provided string %provided% is the same as %string%'))
                    ->code('%provided%', $string)
                    ->code('%string%', $this->string)
                    ->toString()
            );
        }

        return $this;
    }

    /**
     * @throws StrNotContainsException
     */
    public function contains(string $string): StrAssertInterface
    {
        if ((new StrBool($this->string))->contains($string)) {
            return $this;
        }
        throw new StrNotContainsException(
            (new Message('String %string% not contains %provided%'))
                ->code('%provided%', $string)
                ->code('%string%', $this->string)
                ->toString()
        );
    }

    /**
     * @throws StrContainsException
     */
    public function notContains(string $string): StrAssertInterface
    {
        if ((new StrBool($this->string))->contains($string)) {
            throw new StrContainsException(
                (new Message('String %string% contains %provided%'))
                    ->code('%provided%', $string)
                    ->code('%string%', $this->string)
                    ->toString()
            );
        }

        return $this;
    }
}
