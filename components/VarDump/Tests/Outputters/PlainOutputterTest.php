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

use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Outputters\PlainOutputter;
use Chevere\Components\VarDump\VarDumper;
use PHPUnit\Framework\TestCase;

// final class PlainOutputterTest extends TestCase
// {
//     public function testEmpty(): void
//     {
//         $varDumper = new VarDumper(new PlainFormatter);
//         $outputter = new PlainOutputter($varDumper);
//         $line = __LINE__ - 2;
//         // $this->assertSame($varDumper, $outputter->varDumper());
//         $this->assertSame('prepare', $outputter->prepare('prepare'));
//         $this->assertSame('callback', $outputter->callback('callback'));
//         $this->assertSame(__CLASS__ . '->' . __FUNCTION__ . "()\n" . __FILE__ . ':' . $line, $outputter->emit());
//     }

//     public function testNull(): void
//     {
//         $varDumper = new VarDumper(new PlainFormatter, null);
//         $outputter = new PlainOutputter($varDumper);
//         $line = __LINE__ - 2;
//         $this->assertSame(__CLASS__ . '->' . __FUNCTION__ . "()\n" . __FILE__ . ':' . $line . "\n\n" . 'Arg#1 null', $outputter->emit());
//     }

//     public function testWithAnonClass(): void
//     {
//         $anonClass = new class() {
//         };
//         $varDumper = new VarDumper(new PlainFormatter, $anonClass);
//         $outputter = new PlainOutputter($varDumper);
//         $line = __LINE__ - 2;
//         $this->assertStringStartsWith(
//             __CLASS__ . '->' . __FUNCTION__ . "()\n" . __FILE__ . ':' . $line . "\n\n" . 'Arg#1 object class@anonymous' . __FILE__,
//             $outputter->emit()
//         );
//     }
// }
