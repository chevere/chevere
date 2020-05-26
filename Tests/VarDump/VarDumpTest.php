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

use Chevere\Components\VarDump\Formatters\ConsoleFormatter;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Outputters\ConsoleOutputter;
use Chevere\Components\VarDump\Outputters\PlainOutputter;
use Chevere\Components\VarDump\VarDump;
use Chevere\Components\Writers\StreamWriter;
use Chevere\Interfaces\VarDump\VarDumpInterface;
use Chevere\Interfaces\Writers\WriterInterface;
use Laminas\Diactoros\StreamFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use stdClass;

final class VarDumpTest extends TestCase
{
    private function getVarDump(WriterInterface $writer): VarDumpInterface
    {
        return new VarDump($writer, new PlainFormatter, new PlainOutputter);
    }

    private function getStream(string $for = ''): StreamInterface
    {
        return (new StreamFactory)->createStream('');
    }

    public function testConstruct(): void
    {
        $stream = $this->getStream();
        $writer = new StreamWriter($stream);
        $varDump = $this->getVarDump($writer);
        $this->assertSame(0, $varDump->shift());
        $this->assertSame([], $varDump->vars());
    }

    public function testWithVars(): void
    {
        $stream = $this->getStream();
        $writer = new StreamWriter($stream);
        $var = new stdClass;
        $varDump = $this->getVarDump($writer)->withVars($var);
        $this->assertEquals([$var], $varDump->vars());
        $varDump->stream();
    }

    public function testWithShift(): void
    {
        $stream = $this->getStream();
        $writer = new StreamWriter($stream);
        $varDump = $this->getVarDump($writer)->withShift(1);
        $this->assertEquals(1, $varDump->shift());
        $varDump->stream();
    }
}
