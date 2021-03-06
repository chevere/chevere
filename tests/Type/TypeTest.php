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

namespace Chevere\Tests\Type;

use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Type\TypeInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

final class TypeTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Type('TypeSome');
    }

    public function testTypes(): void
    {
        $resource = fopen(__FILE__, 'r');
        if (is_resource($resource) === false) {
            $this->markTestIncomplete('Unable to open ' . __FILE__);
        }
        $scalars = ['boolean', 'integer', 'float', 'string'];
        foreach ([
            Type::BOOLEAN => true,
            Type::INTEGER => 1,
            Type::FLOAT => 13.13,
            Type::STRING => 'test',
            Type::ARRAY => ['test'],
            Type::OBJECT => new stdClass(),
            Type::CALLABLE => 'phpinfo',
            Type::ITERABLE => [4, 2, 1, 3],
            Type::NULL => null,
            Type::RESOURCE => $resource,
        ] as $key => $val) {
            $type = new Type($key);
            $this->assertSame($key, $type->primitive());
            $this->assertSame($key, $type->typeHinting());
            $this->assertTrue($type->validate($val));
            $this->assertSame(in_array($key, $scalars, true), $type->isScalar());
        }
        /** @var resource $resource */
        fclose($resource);
    }

    public function testClassName(): void
    {
        $type = new Type(self::class);
        $this->assertSame(Type::PRIMITIVE_CLASS_NAME, $type->primitive());
        $this->assertSame(self::class, $type->typeHinting());
        $this->assertTrue($type->validate(new self()));
        $this->assertFalse($type->isScalar());
    }

    public function testInterfaceName(): void
    {
        $type = new Type(TypeInterface::class);
        $this->assertSame(Type::PRIMITIVE_INTERFACE_NAME, $type->primitive());
        $this->assertSame(TypeInterface::class, $type->typeHinting());
        $this->assertTrue($type->validate(new Type(Type::STRING)));
    }
}
