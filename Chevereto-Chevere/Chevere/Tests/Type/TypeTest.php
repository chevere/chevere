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
use Chevere\Components\Type\Interfaces\TypeInterface;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    public function testTypes(): void
    {
        foreach ([
            TypeInterface::BOOLEAN => true,
            TypeInterface::INTEGER => 1,
            TypeInterface::FLOAT => 13.13,
            TypeInterface::STRING => 'test',
            TypeInterface::ARRAY => ['test'],
            TypeInterface::OBJECT => new self(),
            TypeInterface::CALLABLE => 'phpinfo',
            TypeInterface::ITERABLE => [4, 2, 1, 3],
            TypeInterface::NULL => null,
        ] as $key => $val) {
            $type = new Type($key);
            $this->assertSame($key, $type->primitive());
            $this->assertSame($key, $type->typeHinting());
            $this->assertTrue($type->validate($val));
        }
    }

    public function testResource(): void
    {
        $type = new Type(TypeInterface::RESOURCE);
        $resource = fopen(__FILE__, 'r');
        $this->assertSame(TypeInterface::RESOURCE, $type->primitive());
        $this->assertSame(TypeInterface::RESOURCE, $type->typeHinting());
        $this->assertTrue($type->validate($resource));
        if (false !== $resource) {
            fclose($resource);
        }
    }

    public function testClassName(): void
    {
        $type = new Type(__CLASS__);
        $this->assertSame(TypeInterface::CLASS_NAME, $type->primitive());
        $this->assertSame(__CLASS__, $type->typeHinting());
        $this->assertTrue($type->validate(new self()));
    }

    public function testInterfaceName(): void
    {
        $type = new Type(TypeInterface::class);
        $this->assertSame(TypeInterface::INTERFACE_NAME, $type->primitive());
        $this->assertSame(TypeInterface::class, $type->typeHinting());
        $this->assertTrue($type->validate(new Type(TypeInterface::STRING)));
    }
}
