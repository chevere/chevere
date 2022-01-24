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

use Chevere\VarDump\Processors\VarDumpStringProcessor;
use Chevere\Tests\VarDump\Traits\VarDumperTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class VarDumpStringProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstruct(): void
    {
        foreach (['', 'string', 'cádena', 'another string', '100', 'false'] as $var) {
            $varDumper = $this->getVarDumper($var);
            $processor = new VarDumpStringProcessor($varDumper);
            $expectedInfo = 'length=' . mb_strlen($var);
            $this->assertSame($expectedInfo, $processor->info(), "info:${var}");
            $processor->write();
            $this->assertSame(
                "string ${var} (${expectedInfo})",
                $varDumper->writer()->__toString(),
                "string:${var}"
            );
        }
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new VarDumpStringProcessor($this->getVarDumper(null));
    }
}
