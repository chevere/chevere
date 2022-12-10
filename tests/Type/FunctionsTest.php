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

use function Chevere\Type\getType;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\returnTypeExceptionMessage;
use PHPUnit\Framework\TestCase;

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
        $this->assertSame("Expecting return type {$expected}, type {$expected} provided", $message->__toString());
    }

    public function testTypeFunctions(): void
    {
        $types = ['boolean', 'integer', 'float', 'string', 'array', 'callable', 'iterable', 'resource', 'null'];
        foreach ($types as $v) {
            $name = 'Chevere\\Type\\type' . ucfirst($v);
            /** @var TypeInterface $fn */
            $object = $name();
            $this->assertSame($v, $object->typeHinting());
        }
    }
}
