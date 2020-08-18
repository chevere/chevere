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

use Chevere\Components\Permission\Range;
use Chevere\Exceptions\Core\OutOfRangeException;
use PHPUnit\Framework\TestCase;

final class RangeTest extends TestCase
{
    public function testNullRange(): void
    {
        $value = null;
        $range = new TestRangeNullTest($value);
        $this->assertSame('test_range_null_test', $range->getIdentifier());
        $this->assertSame($value, $range->value());
        $this->assertSame([null, null], $range->getAccept());
        $this->assertTrue($range->isInRange(null));
        $this->assertTrue($range->isInRange(100));
        new TestRangeNullTest(123);
    }

    public function testMinRange(): void
    {
        $value = 11;
        $range = new TestRangeMinTest($value);
        $this->assertSame($value, $range->value());
        $this->assertSame([10, null], $range->getAccept());
        $this->assertTrue($range->isInRange(10));
        $this->assertFalse($range->isInRange(9));
        $this->expectException(OutOfRangeException::class);
        $range = new TestRangeMinTest(9);
    }

    public function testMaxRange(): void
    {
        $value = 9;
        $range = new TestRangeMaxTest($value);
        $this->assertSame($value, $range->value());
        $this->assertSame([null, 10], $range->getAccept());
        $this->assertTrue($range->isInRange(10));
        $this->assertFalse($range->isInRange(11));
        $this->expectException(OutOfRangeException::class);
        $range = new TestRangeMaxTest(11);
    }

    public function testMinMaxRange(): void
    {
        $value = 50;
        $range = new TestRangeMinMaxTest($value);
        $this->assertSame($value, $range->value());
        $this->assertSame([50, 100], $range->getAccept());
        $this->assertTrue($range->isInRange(50));
        $this->assertFalse($range->isInRange(49));
        $this->assertFalse($range->isInRange(101));
        $this->expectException(OutOfRangeException::class);
        new TestRangeMinMaxTest(null);
    }
}

final class TestRangeNullTest extends Range
{
    public function getDefault(): ?int
    {
        return null;
    }
}

final class TestRangeMinTest extends Range
{
    public function getMin(): ?int
    {
        return 10;
    }

    public function getDefault(): ?int
    {
        return 15;
    }
}

final class TestRangeMaxTest extends Range
{
    public function getMax(): ?int
    {
        return 10;
    }

    public function getDefault(): ?int
    {
        return 5;
    }
}

final class TestRangeMinMaxTest extends Range
{
    public function getMin(): ?int
    {
        return 50;
    }

    public function getMax(): ?int
    {
        return 100;
    }

    public function getDefault(): ?int
    {
        return 75;
    }
}
