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
    }

    public function testInteger(): void
    {
    }

    public function testFloat(): void
    {
    }

    public function testString(): void
    {
    }

    public function testArray(): void
    {
    }

    public function testObject(): void
    {
    }

    public function testCallable(): void
    {
    }

    public function testIterable(): void
    {
    }

    public function testResource(): void
    {
    }

    public function testNull(): void
    {
    }

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
