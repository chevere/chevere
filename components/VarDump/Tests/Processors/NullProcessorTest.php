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

use Chevere\Components\VarDump\Processors\NullProcessor;
use Chevere\Components\VarDump\Tests\Traits\VarDumperTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class NullProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstruct(): void
    {
        $var = null;
        $varDumper = $this->getVarDumper($var);
        $processor = new NullProcessor($varDumper);
        $this->assertSame('', $processor->info());
        $processor->write();
        $this->assertSame('null', $varDumper->writer()->toString());
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NullProcessor($this->getVarDumper(''));
    }
}
