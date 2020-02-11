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

use Chevere\Components\Str\Exceptions\StrAssertException;
use Chevere\Components\Str\Interfaces\StrAssertInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Str\StrBool;

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
        throw new StrAssertException(
            (new Message('String %string% is not empty'))
                ->code('%string%', $this->string)
                ->toString()
        );
    }

    public function notEmpty(): StrAssertInterface
    {
        if ((new StrBool($this->string))->empty()) {
            throw new StrAssertException(
                (new Message('String is empty'))
                    ->toString()
            );
        }

        return $this;
    }

    public function ctypeSpace(): StrAssertInterface
    {
        if ((new StrBool($this->string))->ctypeSpace()) {
            return $this;
        }
        throw new StrAssertException(
            (new Message('String %string% is not %algo%'))
                ->code('%string%', $this->string)
                ->strong('%algo%', 'ctype space')
                ->toString()
        );
    }

    public function notCtypeSpace(): StrAssertInterface
    {
        if ((new StrBool($this->string))->ctypeSpace()) {
            throw new StrAssertException(
                (new Message('String %algo% provided'))
                    ->strong('%algo%', 'ctype space')
                    ->toString()
            );
        }

        return $this;
    }

    public function startsWithCtypeDigit(): StrAssertInterface
    {
        if ((new StrBool($this->string))->firstCharCtypeDigit()) {
            return $this;
        }
        throw new StrAssertException(
            (new Message('String %string% does not starts with a %algo% character'))
                ->strong('%string%', $this->string)
                ->strong('%algo%', 'ctype digit')
                ->toString()
        );
    }

    public function notStartsWithCtypeDigit(): StrAssertInterface
    {
        if ((new StrBool($this->string))->firstCharCtypeDigit()) {
            throw new StrAssertException(
                (new Message('String %string% starts with a %algo% character'))
                    ->strong('%string%', $this->string)
                    ->strong('%algo%', 'ctype digit')
                    ->toString()
            );
        }

        return $this;
    }

    /**
     * Detects if a string begins with the given needle.
     *
     * @param string $needle value being searched for
     */
    public function startsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->startsWith($needle)) {
            return $this;
        }
        throw new StrAssertException(
            (new Message('String does not starts with %needle%'))
                ->strong('%needle%', $needle)
                ->toString()
        );
    }

    public function notStartsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->startsWith($needle)) {
            throw new StrAssertException(
                (new Message('String starts with a %needle%'))
                    ->strong('%needle%', $needle)
                    ->toString()
            );
        }

        return $this;
    }

    /**
     * Detects if a string ends with the given needle.
     *
     * @param string $needle value being searched for
     */
    public function endsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->endsWith($needle)) {
            return $this;
        }
        throw new StrAssertException(
            (new Message('String %string% does not ends with %needle%'))
                ->strong('%string%', $this->string)
                ->strong('%needle%', $needle)
                ->toString()
        );
    }

    public function notEndsWith(string $needle): StrAssertInterface
    {
        if ((new StrBool($this->string))->endsWith($needle)) {
            throw new StrAssertException(
                (new Message('String %string% ends with %needle%'))
                    ->strong('%string%', $this->string)
                    ->strong('%needle%', $needle)
                    ->toString()
            );
        }

        return $this;
    }

    /**
     * Assert timing safe string equals comparison.
     *
     * @param string $string user submitted (unsafe) value
     */
    public function same(string $string): StrAssertInterface
    {
        if ((new StrBool($this->string))->same($string)) {
            return $this;
        }
        throw new StrAssertException(
            (new Message('String %string% is not the same'))
                ->strong('%string%', $string)
                ->toString()
        );
    }

    public function notSame(string $string): StrAssertInterface
    {
        if ((new StrBool($this->string))->same($string)) {
            throw new StrAssertException(
                (new Message('String is the same as %string%'))
                    ->strong('%string%', $this->string)
                    ->toString()
            );
        }

        return $this;
    }
}
