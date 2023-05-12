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

use function Chevere\Standard\getBits;
use PHPUnit\Framework\TestCase;

final class GetBitsFunctionTest extends TestCase
{
    public function bitsProvider(): array
    {
        return [
            [1, [1]],
            [2, [2]],
            [3, [1, 2]],
            [4, [4]],
            [5, [1, 4]],
            [6, [2, 4]],
            [7, [1, 2, 4]],
            [8, [8]],
            [9, [1, 8]],
            [10, [2, 8]],
            [123, [1, 2, 8, 16, 32, 64]],
            [255, [1, 2, 4, 8, 16, 32, 64, 128]],
            [777, [1, 8, 256, 512]],
        ];
    }

    /**
     * @dataProvider bitsProvider
     */
    public function testGetBits(int $mask, array $expected): void
    {
        $this->assertSame($expected, getBits($mask));
    }
}
