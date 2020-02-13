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

use Chevere\Components\VarDump\Processors\ArrayProcessor;
use Chevere\Components\VarDump\Tests\Processors\Traits\VarProcessTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ArrayProcessorTest extends TestCase
{
    use VarProcessTrait;

    public function testConstructEmpty(): void
    {
        $processor = new ArrayProcessor($this->getVarProcess([]));
        $this->assertSame('size=0', $processor->info());
        // $this->assertSame('', $processor->value());
    }

    public function testConstruct(): void
    {
        $var = [0, 1, 2, 3];
        $containTpl = '%s => integer %s (length=1)';
        $processor = new ArrayProcessor($this->getVarProcess($var));
        $this->assertSame('size=' . count($var), $processor->info());
        foreach ($var as $int) {
            // $this->assertStringContainsString(str_replace('%s', $int, $containTpl), $processor->value());
        }
    }

    public function testCircularReference(): void
    {
        $var = [];
        $var[] = &$var;
        $expectInfo = 'size=' . count($var);
        $processor = new ArrayProcessor($this->getVarProcess($var));
        $this->assertSame($expectInfo, $processor->info());
        $this->assertSame(
            "array ($expectInfo) " . $processor->circularReference(),
            $this->getWriter()->toString()
        );
    }

    public function testMaxDepth(): void
    {
        $var = [[[]]];
        $var[] = &$var;
        $processor = new ArrayProcessor($this->getVarProcess($var));
        // $this->assertStringContainsString($processor->maxDepthReached(), $processor->value());
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ArrayProcessor($this->getVarProcess(null));
    }
}
