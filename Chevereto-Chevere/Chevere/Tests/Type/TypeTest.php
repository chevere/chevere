<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Type;

use Chevere\Components\Type\Type;
use Chevere\Contracts\Type\TypeContract;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    public function testBoolean(): void
    {
        $type = new Type(TypeContract::BOOLEAN);
        $this->assertSame(TypeContract::BOOLEAN, $type->primitive());
        $this->assertSame(TypeContract::BOOLEAN, $type->typeHinting());
        $this->assertTrue($type->validate(true));
    }

    public function testInteger(): void
    {
        $type = new Type(TypeContract::INTEGER);
        $this->assertSame(TypeContract::INTEGER, $type->primitive());
        $this->assertSame(TypeContract::INTEGER, $type->typeHinting());
        $this->assertTrue($type->validate(1));
    }

    public function testFloat(): void
    {
        $type = new Type(TypeContract::FLOAT);
        $this->assertSame(TypeContract::FLOAT, $type->primitive());
        $this->assertSame(TypeContract::FLOAT, $type->typeHinting());
        $this->assertTrue($type->validate(13.13));
    }

    public function testString(): void
    {
        $type = new Type(TypeContract::STRING);
        $this->assertSame(TypeContract::STRING, $type->primitive());
        $this->assertSame(TypeContract::STRING, $type->typeHinting());
        $this->assertTrue($type->validate('test'));
    }

    public function testArray(): void
    {
        $type = new Type(TypeContract::ARRAY);
        $this->assertSame(TypeContract::ARRAY, $type->primitive());
        $this->assertSame(TypeContract::ARRAY, $type->typeHinting());
        $this->assertTrue($type->validate(['test']));
    }

    public function testObject(): void
    {
        $type = new Type(TypeContract::OBJECT);
        $this->assertSame(TypeContract::OBJECT, $type->primitive());
        $this->assertSame(TypeContract::OBJECT, $type->typeHinting());
        $this->assertTrue($type->validate(new self()));
    }

    public function testCallable(): void
    {
        $type = new Type(TypeContract::CALLABLE);
        $this->assertSame(TypeContract::CALLABLE, $type->primitive());
        $this->assertSame(TypeContract::CALLABLE, $type->typeHinting());
        $this->assertTrue($type->validate('phpinfo'));
    }

    public function testIterable(): void
    {
        $type = new Type(TypeContract::ITERABLE);
        $this->assertSame(TypeContract::ITERABLE, $type->primitive());
        $this->assertSame(TypeContract::ITERABLE, $type->typeHinting());
        $this->assertTrue($type->validate([0, 1, 2, 3]));
    }

    // public function testResource(): void
    // {
    // }

    // public function testNull(): void
    // {
    // }

    public function testClassName(): void
    {
        $type = new Type(__CLASS__);
        $this->assertSame(TypeContract::CLASS_NAME, $type->primitive());
        $this->assertSame(__CLASS__, $type->typeHinting());
        $this->assertTrue($type->validate(new self()));
    }

    public function testInterfaceName(): void
    {
        $type = new Type(TypeContract::class);
        $this->assertSame(TypeContract::INTERFACE_NAME, $type->primitive());
        $this->assertSame(TypeContract::class, $type->typeHinting());
        $this->assertTrue($type->validate(new Type(TypeContract::STRING)));
    }
}
