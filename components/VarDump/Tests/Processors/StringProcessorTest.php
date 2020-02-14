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

use Chevere\Components\VarDump\Processors\StringProcessor;
use Chevere\Components\VarDump\Tests\Traits\VarDumperTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class StringProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstruct(): void
    {
        foreach (['', 'string', 'cádena', 'another string', '100', 'false'] as $var) {
            $varDumper = $this->getVarDumper($var);
            $processor = new StringProcessor($varDumper);
            $expectedInfo = 'length=' . mb_strlen($var);
            $this->assertSame($expectedInfo, $processor->info(), "info:$var");
            $processor->write();
            $this->assertSame(
                "string $var ($expectedInfo)",
                $varDumper->writer()->toString(),
                "string:$var"
            );
        }
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StringProcessor($this->getVarDumper(null));
    }
}
