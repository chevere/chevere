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

    public function testWithLowercase(): void
    {
        $string = 'sTrÍnG';
        $expected = 'stríng';
        $str = new Str($string);
        $strWithLowercase = $str->withLowercase();
        $this->assertNotSame($str, $strWithLowercase);
        $this->assertSame($expected, $strWithLowercase->toString());
    }

    public function testWithUppercase(): void
    {
        $string = 'sTrínG';
        $expected = 'STRÍNG';
        $str = new Str($string);
        $strWithUppercase = $str->withUppercase();
        $this->assertNotSame($str, $strWithUppercase);
        $this->assertSame($expected, $strWithUppercase->toString());
    }

    public function testWithStripWhitespace(): void
    {
        $string = 'st ri ng';
        $expected = 'string';
        $str = new Str($string);
        $strWithStripWhitespace = $str->withStripWhitespace();
        $this->assertNotSame($str, $strWithStripWhitespace);
        $this->assertSame($expected, $strWithStripWhitespace->toString());
    }

    public function testWithStripExtraWhitespace(): void
    {
        $string = 'str  in  g';
        $expected = 'str in g';
        $str = new Str($string);
        $strWithStripExtraWhitespace = $str->withStripExtraWhitespace();
        $this->assertNotSame($str, $strWithStripExtraWhitespace);
        $this->assertSame($expected, $strWithStripExtraWhitespace->toString());
    }

    public function testWithStripNonAlphanumerics(): void
    {
        $string = '$7r |n,;:g! %~';
        $expected = '7rng';
        $str = new Str($string);
        $strWithStripNonAlphanumerics = $str->withStripNonAlphanumerics();
        $this->assertNotSame($str, $strWithStripNonAlphanumerics);
        $this->assertSame(
            $expected,
            $strWithStripNonAlphanumerics->toString()
        );
    }

    public function testWithForwardSlashes(): void
    {
        $string = '\\str\in\\\\g';
        $expected = '/str/in//g';
        $str = new Str($string);
        $strWithForwardSlashes = $str->withForwardSlashes();
        $this->assertNotSame($str, $strWithForwardSlashes);
        $this->assertSame($expected, $strWithForwardSlashes->toString());
    }

    public function testWithLeftTail(): void
    {
        $string = 'string';
        $tail = 'lt';
        $expected = $tail . $string;
        $str = new Str($tail . $tail . $string);
        $strWithLeftTail = $str->withLeftTail($tail);
        $this->assertNotSame($str, $strWithLeftTail);
        $this->assertSame($expected, $strWithLeftTail->toString());
    }

    public function testWithRightTail(): void
    {
        $string = 'string';
        $tail = 'rt';
        $expected = $string . $tail;
        $str = new Str($string . $tail . $tail);
        $strWithRightTail = $str->withRightTail($tail);
        $this->assertNotSame($str, $strWithRightTail);
        $this->assertSame($expected, $strWithRightTail->toString());
    }

    public function testWithReplaceFirst(): void
    {
        $string = 'eést string';
        $search = 'eést ';
        $replace = 'the ';
        $expected = 'the string';
        $str = new Str($string);
        $strWithReplaceFirst = $str->withReplaceFirst($search, $replace);
        $this->assertNotSame($str, $strWithReplaceFirst);
        $this->assertSame($expected, $strWithReplaceFirst->toString());
        $strWithReplaceFirst = $str->withReplaceFirst('X', $replace);
        $this->assertSame($string, $strWithReplaceFirst->toString());
    }

    public function testWithReplaceLast(): void
    {
        $string = 'string.phpé';
        $stringAlt = 'eee';
        $search = '.phpé';
        $replace = '.md';
        $expected = 'string.md';
        $str = new Str($string);
        $strAlt = new Str($stringAlt);
        $strWithReplaceLast = $str->withReplaceLast($search, $replace);
        $this->assertNotSame($str, $strWithReplaceLast);
        $this->assertSame($expected, $strWithReplaceLast->toString());
        $strAltWithReplaceLast = $strAlt->withReplaceLast($search, $replace);
        $this->assertNotSame($strAlt, $strAltWithReplaceLast);
        $this->assertSame($stringAlt, $strAltWithReplaceLast->toString());
    }

    public function testWithReplaceAll(): void
    {
        $string = 'hola mundo po';
        $search = ' ';
        $str = new Str($string);
        $strWithReplaceAll = $str->withReplaceAll($search, '');
        $this->assertNotSame($str, $strWithReplaceAll);
        $this->assertSame(
            str_replace($search, '', $string),
            $strWithReplaceAll->toString()
        );
    }

    public function testWithStripANSIColors(): void
    {
        $string = 'Arg#1 [38;5;245mnull[0m';
        $str = new Str($string);
        $strWithStripANSIColors = $str->withStripANSIColors();
        $this->assertNotSame($str, $strWithStripANSIColors);
        $this->assertSame(
            'Arg#1 null',
            $strWithStripANSIColors->toString()
        );
    }
}
