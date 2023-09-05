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
use function Chevere\Parameter\assertArgument;
use function Chevere\Parameter\assertInteger;
use function Chevere\Parameter\booleanInteger;
use function Chevere\Parameter\integer;

final class FunctionsIntegerTest extends TestCase
{
    public function testInteger(): void
    {
        $parameter = integer();
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        $this->assertSame(null, $parameter->minimum());
        $this->assertSame(null, $parameter->maximum());
        $this->assertSame([], $parameter->accept());
    }

    public function testIntegerOptions(): void
    {
        $description = 'test';
        $default = 5;
        $parameter = integer(
            description: $description,
            default: $default,
            minimum: -100,
            maximum: 100,
        );
        $this->assertSame($description, $parameter->description());
        $this->assertSame($default, $parameter->default());
        $this->assertSame(-100, $parameter->minimum());
        $this->assertSame(100, $parameter->maximum());
        $parameter = integer(accept: [0, 1]);
        $this->assertSame([0, 1], $parameter->accept());
    }

    public function testAssertInteger(): void
    {
        $parameter = integer();
        $this->assertSame(0, assertInteger($parameter, 0));
        $this->assertSame(0, assertArgument($parameter, 0));
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
