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

use Chevere\VarDump\Processors\VarDumpBooleanProcessor;
use Chevere\Tests\VarDump\Traits\VarDumperTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class VarDumpBooleanProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstruct(): void
    {
        foreach ([
            'true' => true,
            'false' => false,
        ] as $info => $var) {
            $varDumper = $this->getVarDumper($var);
            $processor = new VarDumpBooleanProcessor($varDumper);
            $this->assertSame($info, $processor->info(), 'info');
            $processor->write();
            $this->assertSame(
                "boolean ${info}",
                $varDumper->writer()->__toString(),
            );
        }
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new VarDumpBooleanProcessor($this->getVarDumper(null));
    }
}
