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

namespace Chevere\Tests\Standard;

use PHPUnit\Framework\TestCase;
use function Chevere\Standard\arrayFilterBoth;
use function Chevere\Standard\arrayFilterKey;
use function Chevere\Standard\arrayFilterValue;

final class ArrayFilterRecursiveFunctionsTest extends TestCase
{
    public function testArrayFilterValue(): void
    {
        $array = [1, [1, null], [[[null, 1]]]];
        $result = arrayFilterValue($array);
        $this->assertSame(
            [1, [1], [[[
                1 => 1,
            ]]]],
            $result
        );
    }

    public function testArrayFilterValueTypes(): void
    {
        $array = [
            'a' => 1,
            'b' => null,
            'c' => [],
            'd' => [
                'x' => 1,
                'y' => false,
                'z' => [],
                '' => [
                    '' => null,
                ],
            ],
        ];
        $this->assertSame(
            [],
            arrayFilterValue($array, 'is_string')
        );
        $filter = function ($v) {
            return $v === false || $v === null;
        };
        $expected = [
            'b' => null,
            'd' => [
                'y' => false,
                '' => [
                    '' => null,
                ],
            ],
        ];
        $this->assertSame(
            $expected,
            arrayFilterValue($array, $filter)
        );
    }

    public function testArrayFilterKey(): void
    {
        $array = [
            'a' => 1,
            'b' => [
                'c' => [2, 3, 4],
            ],
            'c' => [
                'c' => [],
            ],
        ];
        $filterC = [
            'a' => 1,
            'b' => [],
        ];
        $this->assertSame(
            $filterC,
            arrayFilterKey($array, function ($k) {
                return $k !== 'c';
            })
        );
    }

    public function testArrayFilterBoth(): void
    {
        $array = [
            'a' => 1,
            'b' => [
                'c' => [2, 3, 4],
            ],
            'c' => [
                'c' => [],
            ],
        ];
        $filterC1 = [
            'b' => [],
        ];
        $this->assertSame(
            $filterC1,
            arrayFilterBoth($array, function ($v, $k) {
                return $k !== 'c' && $v !== 1;
            })
        );
    }

    public function testArrayFilterCore(): void
    {
        $array = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
        ];
        $callableA = function ($k) {
            return $k === 'b';
        };
        $callableB = function ($v, $k) {
            return $k === 'b' || $v === 4;
        };
        $this->assertSame(
            array_filter($array, $callableA),
            arrayFilterValue($array, $callableA)
        );
        $this->assertSame(
            array_filter($array, $callableA, ARRAY_FILTER_USE_KEY),
            arrayFilterKey($array, $callableA)
        );
        $this->assertSame(
            array_filter($array, $callableB, ARRAY_FILTER_USE_BOTH),
            arrayFilterBoth($array, $callableB)
        );
    }
}
