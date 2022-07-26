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

use Chevere\String\AssertString;
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
use PHPUnit\Framework\TestCase;

final class AssertStringTest extends TestCase
{
    public function testEmpty(): void
    {
        (new AssertString(''))->empty();
        $this->expectException(NotEmptyException::class);
        (new AssertString(' '))->empty();
    }

    public function testNotEmpty(): void
    {
        (new AssertString(' '))->notEmpty();
        (new AssertString('0'))->notEmpty();
        $this->expectException(EmptyException::class);
        (new AssertString(''))->notEmpty();
    }

    public function testCtypeSpace(): void
    {
        (new AssertString(" \n\t\r"))->ctypeSpace();
        $this->expectException(NotCtypeSpaceException::class);
        (new AssertString('string'))->ctypeSpace();
    }

    public function testNotCtypeSpace(): void
    {
        (new AssertString("\n valid"))->notCtypeSpace();
        $this->expectException(CtypeSpaceException::class);
        (new AssertString(" \n\t\r"))->notCtypeSpace();
    }

    public function testCtypeDigit(): void
    {
        (new AssertString(" \n\t\r"))->ctypeDigit();
        $this->expectException(NotCtypeDigitException::class);
        (new AssertString('string'))->ctypeDigit();
    }

    public function testNotCtypeDigit(): void
    {
        (new AssertString('string'))->notCtypeDigit();
        $this->expectException(CtypeDigitException::class);
        (new AssertString('101'))->notCtypeDigit();
    }

    public function testStartsWithCtypeDigit(): void
    {
        (new AssertString('0string'))->startsWithCtypeDigit();
        $this->expectException(NotStartsWithCtypeDigitException::class);
        (new AssertString('string'))->startsWithCtypeDigit();
    }

    public function testNotStartsWithCtypeDigit(): void
    {
        (new AssertString('string'))->notStartsWithCtypeDigit();
        $this->expectException(StartsWithCtypeDigitException::class);
        (new AssertString('0string'))->notStartsWithCtypeDigit();
    }

    public function testStartsWith(): void
    {
        (new AssertString('á string'))->startsWith('á');
        $this->expectException(NotStartsWithException::class);
        (new AssertString('string'))->startsWith('some');
    }

    public function testNotStartsWith(): void
    {
        (new AssertString('string'))->notStartsWith('other');
        $this->expectException(StartsWithException::class);
        (new AssertString('string'))->notStartsWith('st');
    }

    public function testEndsWith(): void
    {
        (new AssertString('string'))->endsWith('ing');
        $this->expectException(NotEndsWithException::class);
        (new AssertString('string'))->endsWith('another');
    }

    public function testNotEndsWith(): void
    {
        (new AssertString('string'))->notEndsWith('other');
        $this->expectException(EndsWithException::class);
        (new AssertString('string'))->notEndsWith('ing');
    }

    public function testSame(): void
    {
        (new AssertString('string'))->same('string');
        $this->expectException(NotSameException::class);
        (new AssertString('string'))->same('strin');
    }

    public function testNotSame(): void
    {
        (new AssertString('string'))->notSame('algo');
        $this->expectException(SameException::class);
        (new AssertString('string'))->notSame('string');
    }

    public function testContains(): void
    {
        (new AssertString('string'))->contains('trin');
        $this->expectException(NotContainsException::class);
        (new AssertString('string'))->contains('foo');
    }

    public function testNotContains(): void
    {
        (new AssertString('string'))->notContains('algo');
        $this->expectException(ContainsException::class);
        (new AssertString('string'))->notContains('trin');
    }
}
