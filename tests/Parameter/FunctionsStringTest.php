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

use function Chevere\Parameter\assertArgument;
use function Chevere\Parameter\assertString;
use function Chevere\Parameter\datep;
use function Chevere\Parameter\datetimep;
use function Chevere\Parameter\enump;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\stringp;
use function Chevere\Parameter\timep;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FunctionsStringTest extends TestCase
{
    public function testStringp(): void
    {
        $parameter = stringp();
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        $this->assertSame('', assertString($parameter, ''));
    }

    public function testAssertString(): void
    {
        $parameter = stringp();
        $this->assertSame('test', assertString($parameter, 'test'));
        $this->assertSame('0', assertArgument($parameter, '0'));
    }

    public function testEnump(): void
    {
        $parameter = enump();
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        $this->assertSame('', assertString($parameter, ''));
    }

    public function testAssertEnum(): void
    {
        $parameter = enump('foo', 'bar');
        $this->assertSame('foo', assertString($parameter, 'foo'));
        $this->assertSame('bar', assertString($parameter, 'bar'));
        $this->expectException(InvalidArgumentException::class);
        assertString($parameter, 'barr');
    }

    public function testDatepDefault(): void
    {
        $parameter = datep(default: '2023-04-10');
        $this->assertSame('2023-04-10', $parameter->default());
        $this->expectException(InvalidArgumentException::class);
        datep(default: 'fail');
    }

    public function testAssertDatep(): void
    {
        $parameter = datep();
        $this->assertSame('1000-01-01', assertString($parameter, '1000-01-01'));
        $this->assertSame('9999-12-31', assertString($parameter, '9999-12-31'));
        $this->expectException(InvalidArgumentException::class);
        assertString($parameter, '9999-99-99');
    }

    public function testTimepDefault(): void
    {
        $parameter = timep(default: '23:59:59');
        $this->assertSame('23:59:59', $parameter->default());
        $this->expectException(InvalidArgumentException::class);
        timep(default: '999:99:99');
    }

    public function testAssertTimep(): void
    {
        $parameter = timep();
        $this->assertSame('00:00:00', assertString($parameter, '00:00:00'));
        $this->assertSame('999:59:59', assertString($parameter, '999:59:59'));
        $this->expectException(InvalidArgumentException::class);
        assertString($parameter, '9999:99:99');
    }

    public function testDatetimepDefault(): void
    {
        $parameter = datetimep(default: '1000-01-01 23:59:59');
        $this->assertSame('1000-01-01 23:59:59', $parameter->default());
        $this->expectException(InvalidArgumentException::class);
        datetimep(default: '9999-99-99 999:99:99');
    }

    public function testAssertDatetimep(): void
    {
        $parameter = datetimep();
        $this->assertSame('1000-01-01 23:59:59', assertString($parameter, '1000-01-01 23:59:59'));
        $this->expectException(InvalidArgumentException::class);
        assertString($parameter, '9999-99-99 999:99:99');
    }

    public function defaultsProvider(): array
    {
        return [
            [timep()],
            [datep()],
            [datetimep()],
        ];
    }

    public function descriptionsProvider(): array
    {
        return [
            [timep('Test'), 'Test'],
            [datep('Test'), 'Test'],
            [datetimep('Test'), 'Test'],
        ];
    }

    /**
     * @dataProvider defaultsProvider
     */
    public function testFunctionDefaults(ParameterInterface $parameter): void
    {
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
    }

    /**
     * @dataProvider descriptionsProvider
     */
    public function testFunctionDescription(ParameterInterface $parameter, string $description): void
    {
        $this->assertSame($description, $parameter->description());
    }
}
