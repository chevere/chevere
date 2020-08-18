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
use Chevere\Components\Permission\Ranges;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use PHPUnit\Framework\TestCase;

final class RangesTest extends TestCase
{
    public function testEmpty(): void
    {
        $ranges = new Ranges;
        $this->assertCount(0, $ranges);
        $this->assertFalse($ranges->contains(TestRangesTest::class));
        $this->expectException(OutOfBoundsException::class);
        $ranges->get(TestRangesTest::class);
    }

    public function testAdded(): void
    {
        $range = new TestRangesTest(0);
        $ranges = (new Ranges)->withAdded($range);
        $this->assertTrue($ranges->contains(TestRangesTest::class));
        $this->assertEquals($range, $ranges->get(TestRangesTest::class));
        $this->expectException(OverflowException::class);
        $ranges->withAdded($range);
    }

    public function testModify(): void
    {
        $range = new TestRangesTest(0);
        $ranges = (new Ranges)->withAdded($range);
        $rangeModify = new TestRangesTest(0);
        $ranges = $ranges->withModify($rangeModify);
        $this->assertTrue($ranges->contains(TestRangesTest::class));
        $this->assertEquals($rangeModify, $ranges->get(TestRangesTest::class));
        $this->expectException(OutOfBoundsException::class);
        $ranges->withModify(new TestRanges2Test(0));
    }
}

final class TestRangesTest extends Range
{
    public function getDefault(): ?int
    {
        return 0;
    }
}

final class TestRanges2Test extends Range
{
    public function getDefault(): ?int
    {
        return 0;
    }
}
