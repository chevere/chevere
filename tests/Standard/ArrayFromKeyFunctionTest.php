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
use function Chevere\Standard\arrayFromKey;

final class ArrayFromKeyFunctionTest extends TestCase
{
    public function testFromKeyEmpty(): void
    {
        $input = ['foo', 'bar'];
        $result = arrayFromKey($input);
        $expected = [];
        $this->assertSame($expected, $result);
    }

    public function testFromKeyNotExisting(): void
    {
        $input = ['foo', 'bar'];
        $result = arrayFromKey($input, 'not-existing');
        $expected = [];
        $this->assertSame($expected, $result);
    }

    public function testFromKeyIndex(): void
    {
        $input = ['foo', 'bar'];
        $result = arrayFromKey($input, 0);
        $expected = [
            0 => 'foo',
        ];
        $this->assertSame($expected, $result);
    }

    public function testFromKeyNamed(): void
    {
        $input = [
            'foo' => 'bar',
            'bar' => 'foo',
        ];
        $result = arrayFromKey($input, 'foo');
        $expected = [
            'foo' => 'bar',
        ];
        $this->assertSame($expected, $result);
    }
}
