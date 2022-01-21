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

namespace Chevere\Tests\VarDump;

use Chevere\Components\VarDump\Format\VarDumpPlainFormat;
use Chevere\Components\VarDump\Output\VarDumpPlainOutput;
use Chevere\Components\VarDump\VarDump;
use function Chevere\Components\Writer\streamTemp;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Interfaces\VarDump\VarDumpInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use stdClass;

final class VarDumpTest extends TestCase
{
    public function testConstruct(): void
    {
        $varDump = $this->getVarDump();
        $this->assertSame(0, $varDump->shift());
        $this->assertSame([], $varDump->vars());
    }

    public function testWithVars(): void
    {
        $stream = $this->getStream();
        $writer = new StreamWriter($stream);
        $var = new stdClass();
        $varDump = $this->getVarDump();
        $varDumpWithVars = $varDump->withVars($var);
        $this->assertNotSame($varDump, $varDumpWithVars);
        $this->assertEqualsCanonicalizing([$var], $varDumpWithVars->vars());
        $varDumpWithVars->process($writer);
        $line = strval(__LINE__ - 1);
        $hrLine = str_repeat('-', 60);
        $expectedString = "\n"
            . $varDump::class . '->process()'
            . "\n"
            . $hrLine
            . "\n"
            . __FILE__ . ':' . $line
            . "\n\n"
            . 'Arg:0 stdClass#' . spl_object_id($var)
            . "\n" . $hrLine
            . "\n";
        $this->assertSame($expectedString, $writer->__toString());
    }

    public function testWithShift(): void
    {
        $stream = $this->getStream();
        $writer = new StreamWriter($stream);
        $varDump = $this->getVarDump();
        $varDumpWithShift = $varDump->withShift(1);
        $this->assertNotSame($varDump, $varDumpWithShift);
        $this->assertSame(1, $varDumpWithShift->shift());
        $varDumpWithShift->process($writer);
    }

    private function getVarDump(): VarDumpInterface
    {
        return new VarDump(
            new VarDumpPlainFormat(),
            new VarDumpPlainOutput()
        );
    }

    private function getStream(): StreamInterface
    {
        return streamTemp('');
    }
}
