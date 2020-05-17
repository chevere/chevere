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

namespace Chevere\Components\Str\Tests;

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
use Chevere\Components\Str\StrAssert;
use PHPUnit\Framework\TestCase;

final class StrAssertTest extends TestCase
{
    public function testEmpty(): void
    {
        (new StrAssert(''))->empty();
        $this->expectException(StrNotEmptyException::class);
        (new StrAssert(' '))->empty();
    }

    public function testNotEmpty(): void
    {
        (new StrAssert(' '))->notEmpty();
        (new StrAssert('0'))->notEmpty();
        $this->expectException(StrEmptyException::class);
        (new StrAssert(''))->notEmpty();
    }

    public function testCtypeSpace(): void
    {
        (new StrAssert(" \n\t\r"))->ctypeSpace();
        $this->expectException(StrNotCtypeSpaceException::class);
        (new StrAssert('string'))->ctypeSpace();
    }

    public function testNotCtypeSpace(): void
    {
        (new StrAssert("\n valid"))->notCtypeSpace();
        $this->expectException(StrCtypeSpaceException::class);
        (new StrAssert(" \n\t\r"))->notCtypeSpace();
    }

    public function testCtypeDigit(): void
    {
        (new StrAssert(" \n\t\r"))->ctypeDigit();
        $this->expectException(StrNotCtypeDigitException::class);
        (new StrAssert('string'))->ctypeDigit();
    }

    public function testNotCtypeDigit(): void
    {
        (new StrAssert('string'))->notCtypeDigit();
        $this->expectException(StrCtypeDigitException::class);
        (new StrAssert('101'))->notCtypeDigit();
    }

    public function testStartsWithCtypeDigit(): void
    {
        (new StrAssert('0string'))->startsWithCtypeDigit();
        $this->expectException(StrNotStartsWithCtypeDigitException::class);
        (new StrAssert('string'))->startsWithCtypeDigit();
    }

    public function testNotStartsWithCtypeDigit(): void
    {
        (new StrAssert('string'))->notStartsWithCtypeDigit();
        $this->expectException(StrStartsWithCtypeDigitException::class);
        (new StrAssert('0string'))->notStartsWithCtypeDigit();
    }

    public function testStartsWith(): void
    {
        (new StrAssert('string'))->startsWith('st');
        $this->expectException(StrNotStartsWithException::class);
        (new StrAssert('string'))->startsWith('some');
    }

    public function testNotStartsWith(): void
    {
        (new StrAssert('string'))->notStartsWith('other');
        $this->expectException(StrStartsWithException::class);
        (new StrAssert('string'))->notStartsWith('st');
    }

    public function testEndsWith(): void
    {
        (new StrAssert('string'))->endsWith('ing');
        $this->expectException(StrNotEndsWithException::class);
        (new StrAssert('string'))->endsWith('another');
    }

    public function testNotEndsWith(): void
    {
        (new StrAssert('string'))->notEndsWith('other');
        $this->expectException(StrEndsWithException::class);
        (new StrAssert('string'))->notEndsWith('ing');
    }

    public function testSame(): void
    {
        (new StrAssert('string'))->same('string');
        $this->expectException(StrNotSameException::class);
        (new StrAssert('string'))->same('strin');
    }

    public function testNotSame(): void
    {
        (new StrAssert('string'))->notSame('algo');
        $this->expectException(StrSameException::class);
        (new StrAssert('string'))->notSame('string');
    }

    public function testContains(): void
    {
        (new StrAssert('string'))->contains('trin');
        $this->expectException(StrNotContainsException::class);
        (new StrAssert('string'))->contains('foo');
    }

    public function testNotContains(): void
    {
        (new StrAssert('string'))->notContains('algo');
        $this->expectException(StrContainsException::class);
        (new StrAssert('string'))->notContains('trin');
    }
}
