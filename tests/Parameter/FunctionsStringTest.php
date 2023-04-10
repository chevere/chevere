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
use function Chevere\Parameter\enump;
use function Chevere\Parameter\stringp;
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

    public function testDatep(): void
    {
        $parameter = datep();
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        $parameter = datep('Test', '2023-04-10');
        $this->assertSame('Test', $parameter->description());
        $this->assertSame('2023-04-10', $parameter->default());
    }

    public function testAssertDatep(): void
    {
        $parameter = datep();
        $this->assertSame('1000-01-01', assertString($parameter, '1000-01-01'));
        $this->assertSame('9999-12-31', assertString($parameter, '9999-12-31'));
    }
}
