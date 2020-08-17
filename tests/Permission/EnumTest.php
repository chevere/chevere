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

namespace Chevere\Tests\Permission;

use Chevere\Components\Permission\Enum;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use PHPUnit\Framework\TestCase;

final class EnumTest extends TestCase
{
    public function testInvalidClass(): void
    {
        $this->expectException(LogicException::class);
        new TestNullEnumTest('test');
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TestEnumTest('invalid argument');
    }

    public function testConstruct(): void
    {
        $enum = new TestEnumTest(TestEnumTest::AUTO);
        $this->assertSame(TestEnumTest::AUTO, $enum->value());
    }
}

final class TestNullEnumTest extends Enum
{
}

final class TestEnumTest extends Enum
{
    const AUTO = 'auto';
    const MANUAL = 'manual';
    const DISABLE = 'disable';

    public function getAccept(): array
    {
        return [self::AUTO, self::MANUAL, self::DISABLE];
    }
}
