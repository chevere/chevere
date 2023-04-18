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

namespace Chevere\Tests\Extra;

use function Chevere\Extra\array_filter_recursive;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    public function testArrayFilterRecursive(): void
    {
        $array = [1, [1, null]];
        $this->assertSame([1, [1]], array_filter_recursive($array));
        $array = [
            'a' => 1,
            'b' => null,
            'c' => [
                'a' => 1,
                'b' => null,
            ],
        ];
        $filtered = [
            'a' => 1,
            'c' => [
                'a' => 1,
            ],
        ];
        $this->assertSame($filtered, array_filter_recursive($array));
        $this->assertSame($filtered, array_filter_recursive($array, 'is_int'));
        $this->assertSame([], array_filter_recursive($array, 'is_string'));
        $modes = [
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
            array_filter($modes, $callableA, ARRAY_FILTER_USE_KEY),
            array_filter_recursive($modes, $callableA, ARRAY_FILTER_USE_KEY)
        );
        $this->assertSame(
            array_filter($modes, $callableB, ARRAY_FILTER_USE_BOTH),
            array_filter_recursive($modes, $callableB, ARRAY_FILTER_USE_BOTH)
        );
    }
}
