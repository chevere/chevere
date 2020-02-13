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

namespace Chevere\Components\VarDump\Tests;

use Chevere\Components\VarDump\VarDumpeable;
use stdClass;
use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\VarDumper;
use PHPUnit\Framework\TestCase;

// final class VarFormatTest extends TestCase
// {
//     public function getVarFormat($var): VarProcessInterface
//     {
//         return
//             new VarProcess(
//                 new VarDumpeable($var),
//                 new PlainFormatter()
//             );
//     }

//     public function testConstructNull(): void
//     {
//         $formatter = new PlainFormatter();
//         $varDump = new VarProcess(new VarDumpeable(null), $formatter);
//         $this->assertSame($formatter, $varDump->formatter());
//         $this->assertSame(0, $varDump->indent());
//         $this->assertSame(0, $varDump->depth());
//         $this->assertSame('', $varDump->indentString());
//         $this->assertSame('', $varDump->toString());
//         $varDump = $varDump->withProcessor();
//         $this->assertSame('null', $varDump->toString());
//     }

//     public function testWithIndent(): void
//     {
//         $indent = 10001;
//         $varDump = $this->getVarFormat(null)
//             ->withIndent($indent);
//         $this->assertSame($indent, $varDump->indent());
//         $this->assertTrue($indent <= strlen($varDump->indentString()));
//     }

//     public function testWithDepth(): void
//     {
//         $depth = 4;
//         $varDump = $this->getVarFormat(null)
//             ->withDepth($depth);
//         $this->assertSame($depth, $varDump->depth());
//     }

//     public function testProcessSimpleTypes(): void
//     {
//         $types = [
//             [null, 'null'],
//             [true, 'boolean true'],
//             [1, 'integer 1 (length=1)'],
//             ['', 'string (length=0)'],
//             [[], 'array (size=0)'],
//             [new stdClass, 'object stdClass']
//         ];
//         foreach ($types as $values) {
//             $varDump = $this->getVarFormat($values[0])
//                 ->withProcessor();
//             $this->assertSame($values[1], $varDump->toString());
//         }
//     }
// }
