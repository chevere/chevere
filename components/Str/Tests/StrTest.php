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

use Chevere\Components\Str\Str;
use PHPUnit\Framework\TestCase;

final class StrTest extends TestCase
{
    public function testToString(): void
    {
        $string = 'string';
        $this->assertSame($string, (string) new Str($string));
    }

    public function testLowercase(): void
    {
        $string = 'sTrÍnG';
        $expected = 'stríng';
        $this->assertSame(
            $expected,
            (string) (new Str($string))->lowercase()
        );
    }

    public function testStripWhitespace(): void
    {
        $string = 'st ri ng';
        $expected = 'string';
        $this->assertSame(
            $expected,
            (string) (new Str($string))->stripWhitespace()
        );
    }

    public function testStripExtraWhitespace(): void
    {
        $string = 'str  in  g';
        $expected = 'str in g';
        $this->assertSame(
            $expected,
            (string) (new Str($string))->stripExtraWhitespace()
        );
    }

    public function testStripNonAlphanumerics(): void
    {
        $string = '$7r |n,;:g! %~';
        $expected = '7rng';
        $this->assertSame(
            $expected,
            (string) (new Str($string))->stripNonAlphanumerics()
        );
    }

    public function testForwardSlashes(): void
    {
        $string = '\\str\in\\\\g';
        $expected = '/str/in//g';
        $this->assertSame(
            $expected,
            (string) (new Str($string))->forwardSlashes()
        );
    }

    public function testLeftTail(): void
    {
        $string = 'string';
        $tail = 'lt';
        $expected = $tail . $string;
        $this->assertSame(
            $expected,
            (string) (new Str($tail . $tail . $string))->leftTail($tail)
        );
    }

    public function testRightTail(): void
    {
        $string = 'string';
        $tail = 'rt';
        $expected = $string . $tail;
        $this->assertSame(
            $expected,
            (string) (new Str($string . $tail . $tail))->rightTail($tail)
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
            (string) (new Str($string))->replaceFirst($search, $replace)
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
            (string) (new Str($string))->replaceLast($search, $replace)
        );
    }
}
