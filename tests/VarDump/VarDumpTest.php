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
use function Chevere\Components\Writer\streamForString;
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
        $varDump = $this->getVarDump()->withVars($var);
        $this->assertEqualsCanonicalizing([$var], $varDump->vars());
        $varDump->process($writer);
    }

    public function testWithShift(): void
    {
        $stream = $this->getStream();
        $writer = new StreamWriter($stream);
        $varDump = $this->getVarDump()->withShift(1);
        $this->assertSame(1, $varDump->shift());
        $varDump->process($writer);
    }

    private function getVarDump(): VarDumpInterface
    {
        return new VarDump(new VarDumpPlainFormatter(), new VarDumpPlainOutputter());
    }

    private function getStream(): StreamInterface
    {
        return streamForString('');
    }
}
