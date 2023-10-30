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

use Chevere\Parameter\Interfaces\ParameterInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\assertArgument;
use function Chevere\Parameter\assertString;
use function Chevere\Parameter\boolString;
use function Chevere\Parameter\date;
use function Chevere\Parameter\datetime;
use function Chevere\Parameter\enum;
use function Chevere\Parameter\intString;
use function Chevere\Parameter\string;
use function Chevere\Parameter\time;

final class FunctionsStringTest extends TestCase
{
    public function testStringp(): void
    {
        $parameter = string();
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        $this->assertSame('', assertString($parameter, ''));
    }

    public function testAssertString(): void
    {
        $parameter = string();
        $this->assertSame('test', assertString($parameter, 'test'));
        $this->assertSame('0', assertArgument($parameter, '0'));
    }

    public function testEnum(): void
    {
        $parameter = enum('test');
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        $this->assertSame('test', assertString($parameter, 'test'));
    }

    public function testAssertEnum(): void
    {
        $parameter = enum('foo', 'bar');
        $this->assertSame('foo', assertString($parameter, 'foo'));
        $this->assertSame('bar', assertString($parameter, 'bar'));
        $this->expectException(InvalidArgumentException::class);
        assertString($parameter, 'barr');
    }

    public function testDatepDefault(): void
    {
        $parameter = date(default: '2023-04-10');
        $this->assertSame('2023-04-10', $parameter->default());
        $this->expectException(InvalidArgumentException::class);
        date(default: 'fail');
    }

    public function testAssertDatep(): void
    {
        $parameter = date();
        $this->assertSame('1000-01-01', assertString($parameter, '1000-01-01'));
        $this->assertSame('9999-12-31', assertString($parameter, '9999-12-31'));
        $this->expectException(InvalidArgumentException::class);
        assertString($parameter, '9999-99-99');
    }

    public function testTimepDefault(): void
    {
        $parameter = time(default: '23:59:59');
        $this->assertSame('23:59:59', $parameter->default());
        $this->expectException(InvalidArgumentException::class);
        time(default: '999:99:99');
    }

    public function testAssertTimep(): void
    {
        $parameter = time();
        $this->assertSame('00:00:00', assertString($parameter, '00:00:00'));
        $this->assertSame('999:59:59', assertString($parameter, '999:59:59'));
        $this->expectException(InvalidArgumentException::class);
        assertString($parameter, '9999:99:99');
    }

    public function testDatetimepDefault(): void
    {
        $parameter = datetime(default: '1000-01-01 23:59:59');
        $this->assertSame('1000-01-01 23:59:59', $parameter->default());
        $this->expectException(InvalidArgumentException::class);
        datetime(default: '9999-99-99 999:99:99');
    }

    public function testAssertDatetimep(): void
    {
        $parameter = datetime();
        $this->assertSame('1000-01-01 23:59:59', assertString($parameter, '1000-01-01 23:59:59'));
        $this->expectException(InvalidArgumentException::class);
        assertString($parameter, '9999-99-99 999:99:99');
    }

    public function testBoolString(): void
    {
        $string = boolString();
        $this->assertSame('', $string->description());
        $this->assertNull($string->default());
        $this->expectException(InvalidArgumentException::class);
        boolString(default: '2');
    }

    public static function boolStringArgumentsProvider(): array
    {
        return [
            ['foo', '1'],
            ['bar', '0'],
        ];
    }

    /**
     * @dataProvider boolStringArgumentsProvider
     */
    public function testBoolStringArguments(string $description, string $default): void
    {
        $string = boolString($description, $default);
        $this->assertSame($description, $string->description());
        $this->assertSame($default, $string->default());
    }

    public function defaultsProvider(): array
    {
        return [
            [time(), 'hh:mm:ss'],
            [date(), 'YYYY-MM-DD'],
            [datetime(), 'YYYY-MM-DD hh:mm:ss'],
        ];
    }

    public function descriptionsProvider(): array
    {
        return [
            [time('Test'), 'Test'],
            [date('Test'), 'Test'],
            [datetime('Test'), 'Test'],
        ];
    }

    /**
     * @dataProvider defaultsProvider
     */
    public function testFunctionDefaults(ParameterInterface $parameter, string $description): void
    {
        $this->assertSame($description, $parameter->description());
        $this->assertSame(null, $parameter->default());
    }

    /**
     * @dataProvider descriptionsProvider
     */
    public function testFunctionDescription(ParameterInterface $parameter, string $description): void
    {
        $this->assertSame($description, $parameter->description());
    }

    public function testIntString(): void
    {
        $parameter = intString();
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        assertString($parameter, '0');
        assertString($parameter, '1');
        $this->expectException(InvalidArgumentException::class);
        assertString($parameter, '1abc');
    }
}
