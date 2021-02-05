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

use function Chevere\Components\Type\getType;
use function Chevere\Components\Type\returnTypeExceptionMessage;
use Chevere\Interfaces\Type\TypeInterface;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    public function testVarType(): void
    {
        $table = [
            'object' => $this,
            'float' => 10.10,
        ];
        foreach ($table as $type => $var) {
            $this->assertSame($type, getType($var));
        }
    }

    public function testReturnTypeExceptionMessage(): void
    {
        $expected = 'string';
        $message = returnTypeExceptionMessage($expected, $expected);
        $this->assertSame("Expecting return type ${expected}, type ${expected} provided", $message->toString());
    }

    public function testTypeFunctions(): void
    {
        $types = ['bool', 'int', 'float', 'string', 'array', 'callable', 'iterable', 'resource', 'null'];
        foreach ($types as $v) {
            $name = 'Chevere\\Components\\Type\\type' . ucfirst($v);
            /** @var TypeInterface $fn */
            $object = $name();
            $this->assertSame($v, $object->typeHinting());
        }
    }
}
