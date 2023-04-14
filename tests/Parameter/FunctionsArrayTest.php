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

use function Chevere\Parameter\arrayOptional;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\arrayRequired;
use function Chevere\Parameter\assertArray;
use function Chevere\Parameter\integer;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\string;
use Chevere\Throwable\Errors\ArgumentCountError;
use PHPUnit\Framework\TestCase;

final class FunctionsArrayTest extends TestCase
{
    public function testArrayp(): void
    {
        $parameter = arrayp();
        $this->assertCount(0, $parameter->parameters());
        $integer = integer();
        $string = string();
        $parameter = arrayp(
            required: parameters(a: $integer),
            optional: parameters(b: $string),
        );
        $this->assertCount(2, $parameter->parameters());
        $this->assertSame($integer, $parameter->parameters()->get('a'));
        $this->assertSame($string, $parameter->parameters()->get('b'));
        $this->assertSame(['a'], $parameter->parameters()->requiredKeys());
        $this->assertSame(['b'], $parameter->parameters()->optionalKeys());
    }

    public function testArrayRequiredEmpty(): void
    {
        $parameter = arrayRequired();
        $this->assertSame([], assertArray($parameter, []));
        $this->expectException(ArgumentCountError::class);
        assertArray($parameter, [[]]);
    }

    public function testArrayRequired(): void
    {
        $parameter = arrayRequired(a: integer());
        $array = [
            'a' => 1,
        ];
        $this->assertSame($array, assertArray($parameter, $array));
        $this->expectException(ArgumentCountError::class);
        assertArray($parameter, []);
    }

    public function testArrayRequiredOptional(): void
    {
        $parameter = arrayRequired()->withOptional(a: integer());
        $array = [];
        $this->assertSame($array, assertArray($parameter, $array));
    }

    public function testArrayDefaults(): void
    {
        $parameter = arrayRequired(a: integer(default: 10));
        $array = [];
        $expected = [
            'a' => 10,
        ];
        $this->assertSame($expected, assertArray($parameter, $array));
    }

    public function testArrayOptionalDefaults(): void
    {
        $parameter = arrayRequired()->withOptional(a: integer(default: 10));
        $array = [];
        $expected = [
            'a' => 10,
        ];
        $this->assertSame($expected, assertArray($parameter, $array));
    }

    public function testArrayOptional(): void
    {
        $parameter = arrayOptional();
        $this->assertEquals(arrayRequired(), $parameter);
        $parameter = arrayOptional(a: integer());
        $empty = [];
        $expected = [
            'a' => 1,
        ];
        $this->assertSame($empty, assertArray($parameter, $empty));
        $this->assertSame($expected, assertArray($parameter, $expected));
        $parameter = arrayOptional(a: integer(default: 123));
        $expected = [
            'a' => 123,
        ];
        $this->assertSame($expected, assertArray($parameter, $empty));
        $this->assertSame($expected, assertArray($parameter, $expected));
    }
}
