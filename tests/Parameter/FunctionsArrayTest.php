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

use Chevere\Parameter\ArrayStringParameter;
use Chevere\Throwable\Errors\ArgumentCountError;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\arrayString;
use function Chevere\Parameter\assertArray;
use function Chevere\Parameter\assertArrayString;
use function Chevere\Parameter\int;
use function Chevere\Parameter\string;

final class FunctionsArrayTest extends TestCase
{
    public function testArrayp(): void
    {
        $parameter = arrayp();
        $this->assertCount(0, $parameter->parameters());
        $integer = int();
        $string = string();
        $parameter = arrayp(a: $integer)->withOptional(b: $string);
        $this->assertCount(2, $parameter->parameters());
        $this->assertSame($integer, $parameter->parameters()->get('a'));
        $this->assertSame($string, $parameter->parameters()->get('b'));
        $this->assertSame(['a'], $parameter->parameters()->requiredKeys()->toArray());
        $this->assertSame(['b'], $parameter->parameters()->optionalKeys()->toArray());
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
        $parameter = arrayp(a: int());
        $array = [
            'a' => 1,
        ];
        $this->assertSame($array, assertArray($parameter, $array));
        $this->expectException(ArgumentCountError::class);
        assertArray($parameter, []);
    }

    public function testArrayRequiredOptional(): void
    {
        $parameter = arrayp()->withOptional(a: int());
        $array = [
            'a' => 123,
        ];
        $this->assertSame($array, assertArray($parameter, $array));
    }

    public function testArrayDefaults(): void
    {
        $parameter = arrayp(a: int(default: 10));
        $array = [];
        $expected = [
            'a' => 10,
        ];
        $this->assertSame($expected, assertArray($parameter, $array));
    }

    public function testArrayOptionalDefaults(): void
    {
        $parameter = arrayp()->withOptional(a: int(default: 10));
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
        $parameter = arrayp()->withOptional(a: int());
        $expected = [
            'a' => 1,
        ];
        $this->assertSame($expected, assertArray($parameter, $expected));
        $parameter = arrayp()->withOptional(a: int(default: 123));
        $expected = [
            'a' => 123,
        ];
        $this->assertSame($expected, assertArray($parameter, $expected));
    }

    public function testArrayString(): void
    {
        $string = string();
        $parameter = arrayString(foo: $string);
        $expected = [
            'foo' => 'bar',
        ];
        $this->assertSame($expected, assertArrayString($parameter, $expected));
        $new = new ArrayStringParameter();
        $new = $new->withRequired(foo: $string);
        $this->assertEquals($new, $parameter);
    }
}
