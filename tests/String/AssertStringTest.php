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

namespace Chevere\Tests\String;

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
use Chevere\String\StringAssert;
use PHPUnit\Framework\TestCase;

final class AssertStringTest extends TestCase
{
    public function testEmpty(): void
    {
        (new StringAssert(''))->empty();
        $this->expectException(NotEmptyException::class);
        (new StringAssert(' '))->empty();
    }

    public function testNotEmpty(): void
    {
        (new StringAssert(' '))->notEmpty();
        (new StringAssert('0'))->notEmpty();
        $this->expectException(EmptyException::class);
        (new StringAssert(''))->notEmpty();
    }

    public function testCtypeSpace(): void
    {
        (new StringAssert(" \n\t\r"))->ctypeSpace();
        $this->expectException(NotCtypeSpaceException::class);
        (new StringAssert('string'))->ctypeSpace();
    }

    public function testNotCtypeSpace(): void
    {
        (new StringAssert("\n valid"))->notCtypeSpace();
        $this->expectException(CtypeSpaceException::class);
        (new StringAssert(" \n\t\r"))->notCtypeSpace();
    }

    public function testCtypeDigit(): void
    {
        (new StringAssert(" \n\t\r"))->ctypeDigit();
        $this->expectException(NotCtypeDigitException::class);
        (new StringAssert('string'))->ctypeDigit();
    }

    public function testNotCtypeDigit(): void
    {
        (new StringAssert('string'))->notCtypeDigit();
        $this->expectException(CtypeDigitException::class);
        (new StringAssert('101'))->notCtypeDigit();
    }

    public function testStartsWithCtypeDigit(): void
    {
        (new StringAssert('0string'))->startsWithCtypeDigit();
        $this->expectException(NotStartsWithCtypeDigitException::class);
        (new StringAssert('string'))->startsWithCtypeDigit();
    }

    public function testNotStartsWithCtypeDigit(): void
    {
        (new StringAssert('string'))->notStartsWithCtypeDigit();
        $this->expectException(StartsWithCtypeDigitException::class);
        (new StringAssert('0string'))->notStartsWithCtypeDigit();
    }

    public function testStartsWith(): void
    {
        (new StringAssert('á string'))->startsWith('á');
        $this->expectException(NotStartsWithException::class);
        (new StringAssert('string'))->startsWith('some');
    }

    public function testNotStartsWith(): void
    {
        (new StringAssert('string'))->notStartsWith('other');
        $this->expectException(StartsWithException::class);
        (new StringAssert('string'))->notStartsWith('st');
    }

    public function testEndsWith(): void
    {
        (new StringAssert('string'))->endsWith('ing');
        $this->expectException(NotEndsWithException::class);
        (new StringAssert('string'))->endsWith('another');
    }

    public function testNotEndsWith(): void
    {
        (new StringAssert('string'))->notEndsWith('other');
        $this->expectException(EndsWithException::class);
        (new StringAssert('string'))->notEndsWith('ing');
    }

    public function testSame(): void
    {
        (new StringAssert('string'))->same('string');
        $this->expectException(NotSameException::class);
        (new StringAssert('string'))->same('strin');
    }

    public function testNotSame(): void
    {
        (new StringAssert('string'))->notSame('algo');
        $this->expectException(SameException::class);
        (new StringAssert('string'))->notSame('string');
    }

    public function testContains(): void
    {
        (new StringAssert('string'))->contains('trin');
        $this->expectException(NotContainsException::class);
        (new StringAssert('string'))->contains('foo');
    }

    public function testNotContains(): void
    {
        (new StringAssert('string'))->notContains('algo');
        $this->expectException(ContainsException::class);
        (new StringAssert('string'))->notContains('trin');
    }
}
