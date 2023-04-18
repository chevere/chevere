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

final class FunctionsTest extends TestCase
{
    public function testArrayFilterRecursive(): void
    {
        $array = [1, [1, null]];
        $this->assertSame(
            [1, [1]],
            arrayFilterRecursive($array)
        );
        $array = [
            'a' => 1,
            'b' => null,
            'c' => 0,
            'd' => [
                'x' => 1,
                'y' => false,
                'z' => [],
            ],
            'e' => [],
        ];
        $this->assertSame(
            [],
            arrayFilterRecursive($array, 'is_string')
        );
        $filterEmpty = [
            'a' => 1,
            'd' => [
                'x' => 1,
            ],
        ];
        $this->assertSame(
            $filterEmpty,
            arrayFilterRecursive($array)
        );
        $filterInt = [
            'a' => 1,
            'c' => 0,
            'd' => [
                'x' => 1,
            ],
        ];
        $this->assertSame(
            $filterInt,
            arrayFilterRecursive($array, 'is_int')
        );
        $filterFalseNull = [
            'b' => null,
            'd' => [
                'y' => false,
            ],
        ];
        $this->assertSame(
            $filterFalseNull,
            arrayFilterRecursive($array, function ($v) {
                return $v === false || $v === null;
            })
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
