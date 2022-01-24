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

use Chevere\VarDump\Processors\VarDumpNullProcessor;
use Chevere\Tests\VarDump\Traits\VarDumperTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class VarDumpNullProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstruct(): void
    {
        $var = null;
        $varDumper = $this->getVarDumper($var);
        $processor = new VarDumpNullProcessor($varDumper);
        $this->assertSame('', $processor->info());
        $processor->write();
        $this->assertSame('null', $varDumper->writer()->__toString());
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new VarDumpNullProcessor($this->getVarDumper(''));
    }
}
