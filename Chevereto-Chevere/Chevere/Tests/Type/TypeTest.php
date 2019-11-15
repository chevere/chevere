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
    public function testTypes(): void
    {
        foreach ([
            TypeContract::BOOLEAN => true,
            TypeContract::INTEGER => 1,
            TypeContract::FLOAT => 13.13,
            TypeContract::STRING => 'test',
            TypeContract::ARRAY => ['test'],
            TypeContract::OBJECT => new self(),
            TypeContract::CALLABLE => 'phpinfo',
            TypeContract::ITERABLE => [4, 2, 1, 3],
            TypeContract::NULL => null,
        ] as $key => $val) {
            $type = new Type($key);
            $this->assertSame($key, $type->primitive());
            $this->assertSame($key, $type->typeHinting());
            $this->assertTrue($type->validate($val));
        }
    }

    public function testResource(): void
    {
        $type = new Type(TypeContract::RESOURCE);
        $resource = fopen(__FILE__, 'r');
        $this->assertSame(TypeContract::RESOURCE, $type->primitive());
        $this->assertSame(TypeContract::RESOURCE, $type->typeHinting());
        $this->assertTrue($type->validate($resource));
        fclose($resource);
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
