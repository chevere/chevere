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
use Chevere\Components\Permission\Enums;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use PHPUnit\Framework\TestCase;

final class EnumsTest extends TestCase
{
    public function testEmpty(): void
    {
        $enums = new Enums;
        $this->assertCount(0, $enums);
        $this->assertFalse($enums->contains(TestEnumsTest::class));
        $this->expectException(OutOfBoundsException::class);
        $enums->get(TestEnumsTest::class);
    }

    public function testAdded(): void
    {
        $enum = new TestEnumsTest('');
        $enums = (new Enums)->withAdded($enum);
        $this->assertTrue($enums->contains(TestEnumsTest::class));
        $this->assertEquals($enum, $enums->get(TestEnumsTest::class));
        $this->expectException(OverflowException::class);
        $enums->withAdded($enum);
    }

    public function testModify(): void
    {
        $enum = new TestEnumsTest('');
        $enums = (new Enums)->withAdded($enum);
        $enumModify = new TestEnumsTest('');
        $enums = $enums->withModify($enumModify);
        $this->assertTrue($enums->contains(TestEnumsTest::class));
        $this->assertEquals($enumModify, $enums->get(TestEnumsTest::class));
        $this->expectException(OutOfBoundsException::class);
        $enums->withModify(new TestEnums2Test(''));
    }
}

final class TestEnumsTest extends Enum
{
    public function getDefault(): string
    {
        return '';
    }

    public function getAccept(): array
    {
        return [''];
    }
}

final class TestEnums2Test extends Enum
{
    public function getDefault(): string
    {
        return '';
    }

    public function getAccept(): array
    {
        return [''];
    }
}
