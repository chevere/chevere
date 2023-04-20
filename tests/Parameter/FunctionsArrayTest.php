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

use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\assertArray;
use function Chevere\Parameter\integer;
use function Chevere\Parameter\string;
use Chevere\Throwable\Errors\ArgumentCountError;
use PHPUnit\Framework\TestCase;

final class FunctionsArrayTest extends TestCase
{
    public function testArrayp(): void
    {
        $parameter = arrayp();
        $this->assertCount(0, $parameter->items());
        $integer = integer();
        $string = string();
        $parameter = arrayp(a: $integer)->withOptional(b: $string);
        $this->assertCount(2, $parameter->items());
        $this->assertSame($integer, $parameter->items()->get('a'));
        $this->assertSame($string, $parameter->items()->get('b'));
        $this->assertSame(['a'], $parameter->items()->requiredKeys());
        $this->assertSame(['b'], $parameter->items()->optionalKeys());
    }

    public function testArrayRequiredEmpty(): void
    {
        $parameter = arrayp();
        $this->assertSame([], assertArray($parameter, []));
        $this->expectException(ArgumentCountError::class);
        assertArray($parameter, [[]]);
    }

    public function testArrayRequired(): void
    {
        $parameter = arrayp(a: integer());
        $array = [
            'a' => 1,
        ];
        $this->assertSame($array, assertArray($parameter, $array));
        $this->expectException(ArgumentCountError::class);
        assertArray($parameter, []);
    }

    public function testArrayRequiredOptional(): void
    {
        $parameter = arrayp()->withOptional(a: integer());
        $array = [];
        $this->assertSame($array, assertArray($parameter, $array));
    }

    public function testArrayDefaults(): void
    {
        $parameter = arrayp(a: integer(default: 10));
        $array = [];
        $expected = [
            'a' => 10,
        ];
        $this->assertSame($expected, assertArray($parameter, $array));
    }

    public function testArrayOptionalDefaults(): void
    {
        $parameter = arrayp()->withOptional(a: integer(default: 10));
        $array = [];
        $expected = [
            'a' => 10,
        ];
        $this->assertSame($expected, assertArray($parameter, $array));
    }

    public function testArrayOptional(): void
    {
        $parameter = arrayp();
        $this->assertEquals(arrayp(), $parameter);
        $parameter = arrayp()->withOptional(a: integer());
        $empty = [];
        $expected = [
            'a' => 1,
        ];
        $this->assertSame($empty, assertArray($parameter, $empty));
        $this->assertSame($expected, assertArray($parameter, $expected));
        $parameter = arrayp()->withOptional(a: integer(default: 123));
        $expected = [
            'a' => 123,
        ];
        $this->assertSame($expected, assertArray($parameter, $empty));
        $this->assertSame($expected, assertArray($parameter, $expected));
    }
}
