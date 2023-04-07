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

namespace Chevere\Tests\Parameter;

use function Chevere\Parameter\arrayop;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\assertArray;
use function Chevere\Parameter\integerp;
use Chevere\Throwable\Errors\ArgumentCountError;
use PHPUnit\Framework\TestCase;

final class FunctionsArrayTest extends TestCase
{
    public function testArrayEmpty(): void
    {
        $parameter = arrayp();
        $this->assertSame([], assertArray($parameter, []));
        $this->expectException(ArgumentCountError::class);
        assertArray($parameter, [[]]);
    }

    public function testArrayFixed(): void
    {
        $parameter = arrayp(a: integerp());
        $array = [
            'a' => 1,
        ];
        $this->assertSame($array, assertArray($parameter, $array));
        $this->expectException(ArgumentCountError::class);
        assertArray($parameter, []);
    }

    public function testArrayOptionals(): void
    {
        $parameter = arrayp()->withOptional(a: integerp());
        $array = [];
        $this->assertSame($array, assertArray($parameter, $array));
    }

    public function testArrayDefaults(): void
    {
        $parameter = arrayp(a: integerp(default: 10));
        $array = [];
        $expected = [
            'a' => 10,
        ];
        $this->assertSame($expected, assertArray($parameter, $array));
    }

    public function testArrayOptionalsDefaults(): void
    {
        $parameter = arrayp()->withOptional(a: integerp(default: 10));
        $array = [];
        $expected = [
            'a' => 10,
        ];
        $this->assertSame($expected, assertArray($parameter, $array));
    }

    public function testArrayOptional(): void
    {
        $parameter = arrayop();
        $this->assertEquals(arrayp(), $parameter);
        $parameter = arrayop(a: integerp());
        $empty = [];
        $expected = [
            'a' => 1,
        ];
        $this->assertSame($empty, assertArray($parameter, $empty));
        $this->assertSame($expected, assertArray($parameter, $expected));
        $parameter = arrayop(a: integerp(default: 123));
        $expected = [
            'a' => 123,
        ];
        $this->assertSame($expected, assertArray($parameter, $empty));
        $this->assertSame($expected, assertArray($parameter, $expected));
    }
}
