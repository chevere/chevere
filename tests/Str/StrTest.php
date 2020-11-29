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
            (new Str($string))->withLowercase()->toString()
        );
    }

    public function testUppercase(): void
    {
        $string = 'sTrÍnG';
        $expected = 'STRÍNG';
        $this->assertSame(
            $expected,
            (new Str($string))->withUppercase()->toString()
        );
    }

    public function testStripWhitespace(): void
    {
        $string = 'st ri ng';
        $expected = 'string';
        $this->assertSame(
            $expected,
            (new Str($string))->withStripWhitespace()->toString()
        );
    }

    public function testStripExtraWhitespace(): void
    {
        $string = 'str  in  g';
        $expected = 'str in g';
        $this->assertSame(
            $expected,
            (new Str($string))->withStripExtraWhitespace()->toString()
        );
    }

    public function testStripNonAlphanumerics(): void
    {
        $string = '$7r |n,;:g! %~';
        $expected = '7rng';
        $this->assertSame(
            $expected,
            (new Str($string))->withStripNonAlphanumerics()->toString()
        );
    }

    public function testForwardSlashes(): void
    {
        $string = '\\str\in\\\\g';
        $expected = '/str/in//g';
        $this->assertSame(
            $expected,
            (new Str($string))->withForwardSlashes()->toString()
        );
    }

    public function testLeftTail(): void
    {
        $string = 'string';
        $tail = 'lt';
        $expected = $tail . $string;
        $this->assertSame(
            $expected,
            (new Str($tail . $tail . $string))->withLeftTail($tail)->toString()
        );
    }

    public function testRightTail(): void
    {
        $string = 'string';
        $tail = 'rt';
        $expected = $string . $tail;
        $this->assertSame(
            $expected,
            (new Str($string . $tail . $tail))->withRightTail($tail)->toString()
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
            (new Str($string))->withReplaceFirst($search, $replace)->toString()
        );
    }

    public function testReplaceLast(): void
    {
        $string = 'string.php';
        $stringAlt = 'eee';
        $search = '.php';
        $replace = '.md';
        $expected = 'string.md';
        $this->assertSame(
            $expected,
            (new Str($string))->withReplaceLast($search, $replace)->toString()
        );
        $this->assertSame(
            $stringAlt,
            (new Str($stringAlt))->withReplaceLast($search, $replace)->toString()
        );
    }

    public function testWithReplaceAll(): void
    {
        $string = 'hola mundo po';
        $search = ' ';
        $this->assertSame(
            str_replace($search, '', $string),
            (new Str($string))->withReplaceAll($search, '')->toString()
        );
    }

    public function testWithStripANSIColos(): void
    {
        $string = 'Arg#1 [38;5;245mnull[0m';
        $this->assertSame(
            'Arg#1 null',
            (new Str($string))->withStripANSIColors($string)->toString()
        );
    }
}
