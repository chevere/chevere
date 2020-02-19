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

namespace Chevere\Components\Number\Tests;

use InvalidArgumentException;
use Chevere\Components\Number\Number;
use PHPUnit\Framework\TestCase;
use stdClass;

final class NumberTest extends TestCase
{
    private function getRanges(): array
    {
        $range = count($this->getAbbreviations()) - 1;
        $array = [];
        for ($i = 0; $i <= $range; $i++) {
            $array[] = pow(10, $i);
        }

        return $array;
    }

    private function getAbbreviations(): array
    {
        return [
            0 => '1',
            1 => '10',
            2 => '100',
            3 => '1K',
            4 => '10K',
            5 => '100K',
            6 => '1M',
            7 => '10M',
            8 => '100M',
            9 => '1B',
            10 => '10B',
            11 => '100B',
            12 => '1T',
            13 => '10T',
            14 => '100T',
            15 => '1P',
            16 => '10P',
            17 => '100P',
            18 => '1E',
            19 => '10E',
            20 => '100E',
            21 => '1Z',
            22 => '10Z',
            23 => '100Z',
            24 => '1Y',
        ];
    }

    public function testConstructWithObject(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Number(new stdClass);
    }

    public function testConstructWithString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Number('1.1');
    }

    public function testConstructWithArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Number([]);
    }

    public function testConstructWithNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Number(null);
    }

    public function testConstructWithBoolean(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Number(false);
    }

    public function testConstructWithResource(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $resource = fopen(__FILE__, 'r');
        if (is_resource($resource) === false) {
            $this->markTestIncomplete('Unable to fopen ' . __FILE__);
        }
        new Number($resource);
        if (is_resource($resource)) {
            fclose($resource);
        }
    }

    public function testZero(): void
    {
        $this->assertSame('0', (new Number(0))->toAbbreviate());
    }

    public function testConstruct(): void
    {
        foreach ([
            0 => [0.9, '0.9'],
            1 => [1, '1'],
            2 => [1.1, '1.1'],
            3 => [1.10, '1.1'],
            4 => [1.101, '1.101'],
            4 => [-1500, '-1.5K'],
            4 => [2000, '2K'],
        ] as $array) {
            $number = new Number($array[0]);
            $this->assertSame($array[1], $number->withPrecision(1)->toAbbreviate());
        }
    }

    public function testWithNegativePrecision(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Number(1))->withPrecision(-10);
    }

    public function testWithPrecision(): void
    {
        $var = 12345.12345;
        $number = new Number($var);
        $map = [
            0 => '12K',
            1 => '12.3K',
            2 => '12.35K',
            3 => '12.345K',
            4 => '12.3451K',
            5 => '12.34512K',
            6 => '12.345123K',
            7 => '12.3451235K',
            8 => '12.34512345K',
        ];
        foreach ($map as $pos => $string) {
            $number = $number->withPrecision($pos);
            $this->assertSame($pos, $number->precision());
            $this->assertSame($string, $number->toAbbreviate());
        }
    }

    public function testRanges(): void
    {
        foreach ($this->getRanges() as $pos => $number) {
            $number = new Number($number);
            $this->assertSame(0, $number->precision());
            $this->assertSame($this->getAbbreviations()[$pos], $number->toAbbreviate());
        }
    }
}
