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
use function Chevere\Standard\arrayUnsetKey;

final class ArrayUnsetKeyFunctionTest extends TestCase
{
    public function testUnsetKeyEmpty(): void
    {
        $input = ['foo', 'bar'];
        $result = arrayUnsetKey($input);
        $this->assertSame($input, $result);
    }

    public function testUnsetKeyNotExisting(): void
    {
        $input = ['foo', 'bar'];
        $result = arrayUnsetKey($input, 'not-existing');
        $this->assertSame($input, $result);
    }

    public function testUnsetKeyIndex(): void
    {
        $input = ['foo', 'bar'];
        $result = arrayUnsetKey($input, 0);
        $expected = [
            1 => 'bar',
        ];
        $this->assertSame($expected, $result);
    }

    public function testUnsetKeyNamed(): void
    {
        $input = [
            'foo' => 'bar',
            'bar' => 'foo',
        ];
        $result = arrayUnsetKey($input, 'foo');
        $expected = [
            'bar' => 'foo',
        ];
        $this->assertSame($expected, $result);
    }
}
