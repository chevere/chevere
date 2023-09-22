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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function Chevere\Standard\listPrefixValues;

final class ListPrefixValuesTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            ['foo', 'bar'],
            [1, 1],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFunction($value, $prefix): void
    {
        $list = [$value];
        $result = listPrefixValues($list, $prefix);
        $this->assertSame([$prefix . $value], $result);
    }

    public function testTypeJuggling(): void
    {
        $list = ['wea'];
        $prefix = 1;
        $result = listPrefixValues($list, $prefix);
        $this->assertSame(['1wea'], $result);
    }

    public function testArrayNotList(): void
    {
        $list = [
            1 => 'foo',
            'bar',
        ];
        $this->expectException(InvalidArgumentException::class);
        listPrefixValues($list, 'prefix');
    }
}
