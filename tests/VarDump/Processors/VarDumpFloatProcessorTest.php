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

use Chevere\VarDump\Processors\VarDumpFloatProcessor;
use Chevere\Tests\VarDump\Traits\VarDumperTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class VarDumpFloatProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstruct(): void
    {
        foreach ([0.1, 1.1, 10.0, 2.00, 110.011] as $var) {
            $stringVar = (string) $var;
            $expectedInfo = 'length=' . strlen($stringVar);
            $varDumper = $this->getVarDumper($var);
            $processor = new VarDumpFloatProcessor($varDumper);
            $this->assertSame($expectedInfo, $processor->info());
            $processor->write();
            $this->assertSame(
                "float ${stringVar} (${expectedInfo})",
                $varDumper->writer()->__toString()
            );
        }
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new VarDumpFloatProcessor($this->getVarDumper(100));
    }
}
