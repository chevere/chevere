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

use Chevere\String\ModifyString;
use PHPUnit\Framework\TestCase;

final class ModifyStringTest extends TestCase
{
    public function testToString(): void
    {
        $string = 'string';
        $this->assertSame($string, (new ModifyString($string))->__toString());
    }

    public function testWithLowercase(): void
    {
        $string = 'sTrÍnG';
        $expected = 'stríng';
        $str = new ModifyString($string);
        $strWithLowercase = $str->withLowercase();
        $this->assertNotSame($str, $strWithLowercase);
        $this->assertSame($expected, $strWithLowercase->__toString());
    }

    public function testWithUppercase(): void
    {
        $string = 'sTrínG';
        $expected = 'STRÍNG';
        $str = new ModifyString($string);
        $strWithUppercase = $str->withUppercase();
        $this->assertNotSame($str, $strWithUppercase);
        $this->assertSame($expected, $strWithUppercase->__toString());
    }

    public function testWithStripWhitespace(): void
    {
        $string = 'st ri ng';
        $expected = 'string';
        $str = new ModifyString($string);
        $strWithStripWhitespace = $str->withStripWhitespace();
        $this->assertNotSame($str, $strWithStripWhitespace);
        $this->assertSame($expected, $strWithStripWhitespace->__toString());
    }

    public function testWithStripExtraWhitespace(): void
    {
        $string = 'str  in  g';
        $expected = 'str in g';
        $str = new ModifyString($string);
        $strWithStripExtraWhitespace = $str->withStripExtraWhitespace();
        $this->assertNotSame($str, $strWithStripExtraWhitespace);
        $this->assertSame($expected, $strWithStripExtraWhitespace->__toString());
    }

    public function testWithStripNonAlphanumerics(): void
    {
        $string = '$7r |n,;:g! %~';
        $expected = '7rng';
        $str = new ModifyString($string);
        $strWithStripNonAlphanumerics = $str->withStripNonAlphanumerics();
        $this->assertNotSame($str, $strWithStripNonAlphanumerics);
        $this->assertSame(
            $expected,
            $strWithStripNonAlphanumerics->__toString()
        );
    }

    public function testWithForwardSlashes(): void
    {
        $string = '\\str\in\\\\g';
        $expected = '/str/in//g';
        $str = new ModifyString($string);
        $strWithForwardSlashes = $str->withForwardSlashes();
        $this->assertNotSame($str, $strWithForwardSlashes);
        $this->assertSame($expected, $strWithForwardSlashes->__toString());
    }

    public function testWithLeftTail(): void
    {
        $string = 'string';
        $tail = 'lt';
        $expected = $tail . $string;
        $str = new ModifyString($tail . $tail . $string);
        $strWithLeftTail = $str->withLeftTail($tail);
        $this->assertNotSame($str, $strWithLeftTail);
        $this->assertSame($expected, $strWithLeftTail->__toString());
    }

    public function testWithRightTail(): void
    {
        $string = 'string';
        $tail = 'rt';
        $expected = $string . $tail;
        $str = new ModifyString($string . $tail . $tail);
        $strWithRightTail = $str->withRightTail($tail);
        $this->assertNotSame($str, $strWithRightTail);
        $this->assertSame($expected, $strWithRightTail->__toString());
    }

    public function testWithReplaceFirst(): void
    {
        $string = 'eést string';
        $search = 'eést ';
        $replace = 'the ';
        $expected = 'the string';
        $str = new ModifyString($string);
        $strWithReplaceFirst = $str->withReplaceFirst($search, $replace);
        $this->assertNotSame($str, $strWithReplaceFirst);
        $this->assertSame($expected, $strWithReplaceFirst->__toString());
        $strWithReplaceFirst = $str->withReplaceFirst('X', $replace);
        $this->assertSame($string, $strWithReplaceFirst->__toString());
    }

    public function testWithReplaceLast(): void
    {
        $string = 'string.phpé';
        $stringAlt = 'eee';
        $search = '.phpé';
        $replace = '.md';
        $expected = 'string.md';
        $str = new ModifyString($string);
        $strAlt = new ModifyString($stringAlt);
        $strWithReplaceLast = $str->withReplaceLast($search, $replace);
        $this->assertNotSame($str, $strWithReplaceLast);
        $this->assertSame($expected, $strWithReplaceLast->__toString());
        $strAltWithReplaceLast = $strAlt->withReplaceLast($search, $replace);
        $this->assertNotSame($strAlt, $strAltWithReplaceLast);
        $this->assertSame($stringAlt, $strAltWithReplaceLast->__toString());
    }

    public function testWithReplaceAll(): void
    {
        $string = 'hola mundo po';
        $search = ' ';
        $str = new ModifyString($string);
        $strWithReplaceAll = $str->withReplaceAll($search, '');
        $this->assertNotSame($str, $strWithReplaceAll);
        $this->assertSame(
            str_replace($search, '', $string),
            $strWithReplaceAll->__toString()
        );
    }

    public function testWithStripANSIColors(): void
    {
        $string = 'Arg#1 [38;5;245mnull[0m';
        $str = new ModifyString($string);
        $strWithStripANSIColors = $str->withStripANSIColors();
        $this->assertNotSame($str, $strWithStripANSIColors);
        $this->assertSame(
            'Arg#1 null',
            $strWithStripANSIColors->__toString()
        );
    }
}
