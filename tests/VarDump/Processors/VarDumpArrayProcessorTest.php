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

namespace Chevere\Tests\VarDump\Processors;

use Chevere\VarDump\Interfaces\VarDumpProcessorInterface;
use Chevere\VarDump\Processors\VarDumpArrayProcessor;
use Chevere\Tests\VarDump\Traits\VarDumperTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class VarDumpArrayProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new VarDumpArrayProcessor($this->getVarDumper(null));
    }
    
    public function testConstructEmpty(): void
    {
        $var = [];
        $expectInfo = 'size=' . count($var);
        $varProcess = $this->getVarDumper($var);
        $processor = new VarDumpArrayProcessor($varProcess);
        $this->assertSame(1, $processor->depth());
        $this->assertSame($expectInfo, $processor->info());
        $processor->write();
        $this->assertSame(
            "array (${expectInfo})",
            $varProcess->writer()->__toString()
        );
    }

    public function testX(): void
    {
        $var = [0, 1, 2, 3];
        $expectInfo = 'size=' . count($var);
        $containTpl = '%s => integer %s (length=1)';
        $varProcess = $this->getVarDumper($var);
        $processor = new VarDumpArrayProcessor($varProcess);
        $this->assertSame($expectInfo, $processor->info());
        $processor->write();
        foreach ($var as $int) {
            $this->assertStringContainsString(
                str_replace('%s', (string) $int, $containTpl),
                $varProcess->writer()->__toString()
            );
        }
    }

    public function testCircularReference(): void
    {
        $var = [];
        $var[] = &$var;
        $expectInfo = 'size=' . count($var);
        $varProcess = $this->getVarDumper($var);
        $processor = new VarDumpArrayProcessor($varProcess);
        $this->assertSame($expectInfo, $processor->info());
        $processor->write();
        $this->assertSame(
            "array (${expectInfo}) " . $processor->circularReference(),
            $varProcess->writer()->__toString()
        );
    }

    public function testMaxDepth(): void
    {
        $var = [];
        for ($i = 0; $i <= VarDumpProcessorInterface::MAX_DEPTH; $i++) {
            $var = [$var];
        }
        $varProcess = $this->getVarDumper($var);
        $processor = new VarDumpArrayProcessor($varProcess);
        $processor->write();
        $this->assertStringContainsString($processor->maxDepthReached(), $varProcess->writer()->__toString());
    }
}
