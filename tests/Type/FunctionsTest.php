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

use Chevere\Type\Interfaces\TypeInterface;
use PHPUnit\Framework\TestCase;
use function Chevere\Type\getType;
use function Chevere\Type\returnTypeExceptionMessage;
use function Chevere\Type\typeUnion;

final class FunctionsTest extends TestCase
{
    public function testVariableType(): void
    {
        $table = [
            'object' => $this,
            'float' => 10.10,
            'null' => null,
        ];
        foreach ($table as $type => $variable) {
            $this->assertSame($type, getType($variable));
        }
    }

    public function testReturnTypeExceptionMessage(): void
    {
        $expected = 'string';
        $message = returnTypeExceptionMessage($expected, $expected);
        $this->assertSame("Expecting return type `{$expected}`, type `{$expected}` provided", $message);
    }

    public function testTypeFunctions(): void
    {
        $types = ['bool', 'int', 'float', 'string', 'array', 'callable', 'iterable', 'resource', 'null'];
        foreach ($types as $v) {
            $name = 'Chevere\\Type\\type' . ucfirst($v);
            /** @var TypeInterface $fn */
            $object = $name();
            $this->assertSame($v, $object->typeHinting());
        }
    }

    public function testUnionFunction(): void
    {
        $type = typeUnion();
        $this->assertFalse($type->validate('test'));
        $this->assertTrue($type->validate(['a', 'b', 'c']));
    }
}
