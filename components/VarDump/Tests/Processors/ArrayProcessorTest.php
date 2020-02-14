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

namespace Chevere\Components\VarDump\Tests\Processors;

use Chevere\Components\VarDump\Interfaces\ProcessorInterface;
use Chevere\Components\VarDump\Processors\ArrayProcessor;
use Chevere\Components\VarDump\Tests\Traits\VarDumperTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\stream_for;

final class ArrayProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstructEmpty(): void
    {
        $var = [];
        $expectInfo = 'size=' . count($var);
        $varProcess = $this->getVarDumper($var);
        $processor = new ArrayProcessor($varProcess);
        $this->assertSame($expectInfo, $processor->info());
        $processor->write();
        $this->assertSame(
            "array ($expectInfo)",
            $varProcess->writer()->toString()
        );
    }

    public function testX(): void
    {
        $var = [0, 1, 2, 3];
        $expectInfo = 'size=' . count($var);
        $containTpl = '%s => integer %s (length=1)';
        $varProcess = $this->getVarDumper($var);
        $processor = new ArrayProcessor($varProcess);
        $this->assertSame($expectInfo, $processor->info());
        $processor->write();
        foreach ($var as $int) {
            $this->assertStringContainsString(
                str_replace('%s', $int, $containTpl),
                $varProcess->writer()->toString()
            );
        }
    }

    public function testCircularReference(): void
    {
        $var = [];
        $var[] = &$var;
        $expectInfo = 'size=' . count($var);
        $varProcess = $this->getVarDumper($var);
        $processor = new ArrayProcessor($varProcess);
        $this->assertSame($expectInfo, $processor->info());
        $processor->write();
        $this->assertSame(
            "array ($expectInfo) " . $processor->circularReference(),
            $varProcess->writer()->toString()
        );
    }

    public function testMaxDepth(): void
    {
        $var = [];
        for ($i = 0; $i <= ProcessorInterface::MAX_DEPTH; $i++) {
            $var = [$var];
        }
        $varProcess = $this->getVarDumper($var);
        $processor = new ArrayProcessor($varProcess);
        $processor->write();
        $this->assertStringContainsString($processor->maxDepthReached(), $varProcess->writer()->toString());
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ArrayProcessor($this->getVarDumper(null));
    }
}
