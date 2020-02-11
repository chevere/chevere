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

use Chevere\Components\Str\StrAssert;
use Chevere\Components\Str\Exceptions\StrAssertException;
use PHPUnit\Framework\TestCase;

final class StrAssertTest extends TestCase
{
    public function testEmpty(): void
    {
        (new StrAssert(''))->empty();
        $this->expectException(StrAssertException::class);
        (new StrAssert(' '))->empty();
    }

    public function testNotEmpty(): void
    {
        (new StrAssert(' '))->notEmpty();
        (new StrAssert('0'))->notEmpty();
        $this->expectException(StrAssertException::class);
        (new StrAssert(''))->notEmpty();
    }

    public function testCtypeSpace(): void
    {
        (new StrAssert(" \n\t\r"))->ctypeSpace();
        $this->expectException(StrAssertException::class);
        (new StrAssert('string'))->ctypeSpace();
    }

    public function testNotCtypeSpace(): void
    {
        (new StrAssert("\n valid"))->notCtypeSpace();
        $this->expectException(StrAssertException::class);
        (new StrAssert(" \n\t\r"))->notCtypeSpace();
    }

    public function testStartsWithCtypeDigit(): void
    {
        (new StrAssert('0string'))->startsWithCtypeDigit();
        $this->expectException(StrAssertException::class);
        (new StrAssert('string'))->startsWithCtypeDigit();
    }

    public function testNotStartsWithCtypeDigit(): void
    {
        (new StrAssert('string'))->notStartsWithCtypeDigit();
        $this->expectException(StrAssertException::class);
        (new StrAssert('0string'))->notStartsWithCtypeDigit();
    }

    public function testStartsWith(): void
    {
        (new StrAssert('string'))->startsWith('st');
        $this->expectException(StrAssertException::class);
        (new StrAssert('string'))->startsWith('some');
    }

    public function testNotStartsWith(): void
    {
        (new StrAssert('string'))->notStartsWith('other');
        $this->expectException(StrAssertException::class);
        (new StrAssert('string'))->notStartsWith('st');
    }

    public function testEndsWith(): void
    {
        (new StrAssert('string'))->endsWith('ing');
        $this->expectException(StrAssertException::class);
        (new StrAssert('string'))->endsWith('another');
    }

    public function testNotEndsWith(): void
    {
        (new StrAssert('string'))->notEndsWith('otro');
        $this->expectException(StrAssertException::class);
        (new StrAssert('string'))->notEndsWith('ing');
    }

    public function testSame(): void
    {
        (new StrAssert('string'))->same('string');
        $this->expectException(StrAssertException::class);
        (new StrAssert('string'))->same('strin');
    }

    public function testNotSame(): void
    {
        (new StrAssert('string'))->notSame('algo');
        $this->expectException(StrAssertException::class);
        (new StrAssert('string'))->notSame('string');
    }
}
