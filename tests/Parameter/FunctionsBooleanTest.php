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

use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\boolean;
use function Chevere\Parameter\booleanInteger;
use function Chevere\Parameter\booleanString;

final class FunctionsBooleanTest extends TestCase
{
    public function testBoolean(): void
    {
        $boolean = boolean();
        $this->assertSame('', $boolean->description());
        $this->assertNull($boolean->default());
    }

    public static function booleanArgumentsProvider(): array
    {
        return [
            ['foo', true],
            ['bar', false],
        ];
    }

    /**
     * @dataProvider booleanArgumentsProvider
     */
    public function testBooleanArguments(string $description, bool $default): void
    {
        $boolean = boolean($description, $default);
        $this->assertSame($description, $boolean->description());
        $this->assertSame($default, $boolean->default());
    }

    public function testBooleanString(): void
    {
        $string = booleanString();
        $this->assertSame('', $string->description());
        $this->assertNull($string->default());
        $this->expectException(InvalidArgumentException::class);
        booleanString(default: '2');
    }

    public static function booleanStringArgumentsProvider(): array
    {
        return [
            ['foo', '1'],
            ['bar', '0'],
        ];
    }

    /**
     * @dataProvider booleanStringArgumentsProvider
     */
    public function testBooleanStringArguments(string $description, string $default): void
    {
        $string = booleanString($description, $default);
        $this->assertSame($description, $string->description());
        $this->assertSame($default, $string->default());
    }

    public function testBooleanInteger(): void
    {
        $integer = booleanInteger();
        $this->assertSame('', $integer->description());
        $this->assertNull($integer->default());
        $this->expectException(InvalidArgumentException::class);
        booleanInteger(default: 2);
    }

    public static function booleanIntegerArgumentsProvider(): array
    {
        return [
            ['foo', 1],
            ['bar', 0],
        ];
    }

    /**
     * @dataProvider booleanIntegerArgumentsProvider
     */
    public function testBooleanIntegerArguments(string $description, int $default): void
    {
        $integer = booleanInteger($description, $default);
        $this->assertSame($description, $integer->description());
        $this->assertSame($default, $integer->default());
    }
}
