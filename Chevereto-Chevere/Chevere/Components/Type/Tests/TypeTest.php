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

namespace Chevere\Components\Type\Tests;

use Chevere\Components\Type\Type;
use Chevere\Components\Type\Interfaces\TypeInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

final class TypeTest extends TestCase
{
    public function testTypes(): void
    {
        $resource = fopen(__FILE__, 'r');
        foreach ([
            TypeInterface::BOOLEAN => true,
            TypeInterface::INTEGER => 1,
            TypeInterface::FLOAT => 13.13,
            TypeInterface::STRING => 'test',
            TypeInterface::ARRAY => ['test'],
            TypeInterface::OBJECT => new stdClass,
            TypeInterface::CALLABLE => 'phpinfo',
            TypeInterface::ITERABLE => [4, 2, 1, 3],
            TypeInterface::NULL => null,
            TypeInterface::RESOURCE => $resource,
        ] as $key => $val) {
            $type = new Type($key);
            $this->assertSame($key, $type->primitive());
            $this->assertSame($key, $type->typeHinting());
            $this->assertTrue($type->validate($val));
        }
        fclose($resource);
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
