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

use Chevere\Components\VarDump\Formatters\VarDumpPlainFormatter;
use Chevere\Components\VarDump\Outputters\VarDumpPlainOutputter;
use Chevere\Components\VarDump\VarDump;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Interfaces\VarDump\VarDumpInterface;
use Chevere\Interfaces\Writer\WriterInterface;
use Laminas\Diactoros\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use stdClass;

final class VarDumpTest extends TestCase
{
    private function getVarDump(): VarDumpInterface
    {
        return new VarDump(new VarDumpPlainFormatter, new VarDumpPlainOutputter);
    }

    private function getStream(): StreamInterface
    {
        return (new StreamFactory)->createStream('');
    }

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
        $var = new stdClass;
        $varDump = $this->getVarDump()->withVars($var);
        $this->assertEquals([$var], $varDump->vars());
        $varDump->process($writer);
    }

    public function testWithShift(): void
    {
        $stream = $this->getStream();
        $writer = new StreamWriter($stream);
        $varDump = $this->getVarDump()->withShift(1);
        $this->assertEquals(1, $varDump->shift());
        $varDump->process($writer);
    }
}
