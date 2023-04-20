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

use function Chevere\Standard\arrayFilterRecursive;
use PHPUnit\Framework\TestCase;

final class ArrayFilterRecursiveFunctionsTest extends TestCase
{
    public function testArrayFilterRecursiveEmpty(): void
    {
        $array = [1, [1, null], [[[null, 1]]]];
        $this->assertSame(
            [1, [1], [[[
                1 => 1,
            ]]]],
            arrayFilterRecursive($array)
        );
    }

    public function testArrayFilterRecursiveTypes(): void
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
            arrayFilterRecursive($array, 'is_string')
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
            arrayFilterRecursive($array, $filter)
        );
    }

    public function testArrayFilterRecursiveNested(): void
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
            arrayFilterRecursive($array, function ($k) {
                return $k !== 'c';
            }, ARRAY_FILTER_USE_KEY)
        );
    }

    public function testArrayFilterRecursiveCore(): void
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
            array_filter($array, $callableA, ARRAY_FILTER_USE_KEY),
            arrayFilterRecursive($array, $callableA, ARRAY_FILTER_USE_KEY)
        );
        $this->assertSame(
            array_filter($array, $callableB, ARRAY_FILTER_USE_BOTH),
            arrayFilterRecursive($array, $callableB, ARRAY_FILTER_USE_BOTH)
        );
    }
}
