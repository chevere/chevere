<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Number\Tests;

use Chevere\Components\Number\Number;
use InvalidArgumentException;
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

    public function testConstructObjectArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Number(new stdClass);
    }

    public function testConstructStringArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Number('1.1');
    }

    public function testConstructArrayArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Number([]);
    }

    public function testConstructNullArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Number(null);
    }

    public function testConstructBooleanArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Number(false);
    }

    public function testConstructResourceArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Number(fopen(__FILE__, 'r'));
    }

    public function testConstruct(): void
    {
        foreach ([
            0 => [0, '0'],
            1 => [1, '1'],
            2 => [1.1, '1.1']
        ] as $array) {
            $number = new Number($array[0]);
            $this->assertSame($array[1], $number->toAbbreviate());
        }
    }

    public function testPrecision(): void
    {
        $var = 1652.5;
        $number = new Number($var);
        foreach ([
            0 => '2K',
            1 => '1.7K',
            2 => '1.65K',
            3 => '1.653K',
            4 => '1.6525K',
        ] as $pos => $string) {
            $this->assertSame($string, $number->withPrecision($pos)->toAbbreviate());
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
