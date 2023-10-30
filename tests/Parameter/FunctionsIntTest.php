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
use function Chevere\Parameter\assertInt;
use function Chevere\Parameter\boolInt;
use function Chevere\Parameter\int;

final class FunctionsIntTest extends TestCase
{
    public function testInt(): void
    {
        $parameter = int();
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        $this->assertSame(null, $parameter->minimum());
        $this->assertSame(null, $parameter->maximum());
        $this->assertSame([], $parameter->accept());
    }

    public function testIntOptions(): void
    {
        $description = 'test';
        $default = 5;
        $parameter = int(
            description: $description,
            default: $default,
            minimum: -100,
            maximum: 100,
        );
        $this->assertSame($description, $parameter->description());
        $this->assertSame($default, $parameter->default());
        $this->assertSame(-100, $parameter->minimum());
        $this->assertSame(100, $parameter->maximum());
        $parameter = int(accept: [0, 1]);
        $this->assertSame([0, 1], $parameter->accept());
    }

    public function testAssertInt(): void
    {
        $parameter = int();
        $this->assertSame(0, assertInt($parameter, 0));
        $this->assertSame(0, assertArgument($parameter, 0));
    }

    public function testBoolInt(): void
    {
        $int = boolInt();
        $this->assertSame('', $int->description());
        $this->assertNull($int->default());
        $this->expectException(InvalidArgumentException::class);
        boolInt(default: 2);
    }

    public static function boolIntArgumentsProvider(): array
    {
        return [
            ['foo', 1],
            ['bar', 0],
        ];
    }

    /**
     * @dataProvider boolIntArgumentsProvider
     */
    public function testBoolIntArguments(string $description, int $default): void
    {
        $int = boolInt($description, $default);
        $this->assertSame($description, $int->description());
        $this->assertSame($default, $int->default());
    }
}
