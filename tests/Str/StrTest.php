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

namespace Chevere\Tests\Str;

use Chevere\Components\Str\Str;
use PHPUnit\Framework\TestCase;

final class StrTest extends TestCase
{
    public function testToString(): void
    {
        $string = 'string';
        $this->assertSame($string, (new Str($string))->toString());
    }

    public function testLowercase(): void
    {
        $string = 'sTrÍnG';
        $expected = 'stríng';
        $this->assertSame(
            $expected,
            (new Str($string))->lowercase()->toString()
        );
    }

    public function testUppercase(): void
    {
        $string = 'sTrÍnG';
        $expected = 'STRÍNG';
        $this->assertSame(
            $expected,
            (new Str($string))->uppercase()->toString()
        );
    }

    public function testStripWhitespace(): void
    {
        $string = 'st ri ng';
        $expected = 'string';
        $this->assertSame(
            $expected,
            (new Str($string))->stripWhitespace()->toString()
        );
    }

    public function testStripExtraWhitespace(): void
    {
        $string = 'str  in  g';
        $expected = 'str in g';
        $this->assertSame(
            $expected,
            (new Str($string))->stripExtraWhitespace()->toString()
        );
    }

    public function testStripNonAlphanumerics(): void
    {
        $string = '$7r |n,;:g! %~';
        $expected = '7rng';
        $this->assertSame(
            $expected,
            (new Str($string))->stripNonAlphanumerics()->toString()
        );
    }

    public function testForwardSlashes(): void
    {
        $string = '\\str\in\\\\g';
        $expected = '/str/in//g';
        $this->assertSame(
            $expected,
            (new Str($string))->forwardSlashes()->toString()
        );
    }

    public function testLeftTail(): void
    {
        $string = 'string';
        $tail = 'lt';
        $expected = $tail . $string;
        $this->assertSame(
            $expected,
            (new Str($tail . $tail . $string))->leftTail($tail)->toString()
        );
    }

    public function testRightTail(): void
    {
        $string = 'string';
        $tail = 'rt';
        $expected = $string . $tail;
        $this->assertSame(
            $expected,
            (new Str($string . $tail . $tail))->rightTail($tail)->toString()
        );
    }

    public function testReplaceFirst(): void
    {
        $string = 'ststring';
        $search = 'st';
        $replace = 'the ';
        $expected = 'the string';
        $this->assertSame(
            $expected,
            (new Str($string))->replaceFirst($search, $replace)->toString()
        );
    }

    public function testReplaceLast(): void
    {
        $string = 'stringg';
        $search = 'g';
        $replace = 'o';
        $expected = 'stringo';
        $this->assertSame(
            $expected,
            (new Str($string))->replaceLast($search, $replace)->toString()
        );
    }
}
