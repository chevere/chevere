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

use Chevere\Components\Permission\Condition;
use Chevere\Components\Permission\Conditions;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use PHPUnit\Framework\TestCase;

final class ConditionsTest extends TestCase
{
    public function testEmpty(): void
    {
        $conditions = new Conditions;
        $this->assertCount(0, $conditions);
        $this->assertFalse($conditions->contains(TestConditionsTest::class));
        $this->expectException(OutOfBoundsException::class);
        $conditions->get(TestConditionsTest::class);
    }

    public function testAdded(): void
    {
        $condition = new TestConditionsTest(false);
        $conditions = (new Conditions)->withAdded($condition);
        $this->assertCount(1, $conditions);
        $this->assertTrue($conditions->contains(TestConditionsTest::class));
        $this->assertEquals($condition, $conditions->get(TestConditionsTest::class));
        $this->expectException(OverflowException::class);
        $conditions->withAdded($condition);
    }

    public function testModify(): void
    {
        $condition = new TestConditionsTest(false);
        $conditions = (new Conditions)->withAdded($condition);
        $conditionModify = new TestConditionsTest(true);
        $conditions = $conditions->withModify($conditionModify);
        $this->assertCount(1, $conditions);
        $this->assertTrue($conditions->contains(TestConditionsTest::class));
        $this->assertEquals($conditionModify, $conditions->get(TestConditionsTest::class));
        $this->expectException(OutOfBoundsException::class);
        $conditions->withModify(new TestConditions2Test(false));
    }
}

final class TestConditionsTest extends Condition
{
}

final class TestConditions2Test extends Condition
{
}
