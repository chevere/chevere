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

use stdClass;
use Chevere\Components\VarDump\Processors\BooleanProcessor;
use Chevere\Components\VarDump\Tests\Traits\VarDumperTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BooleanProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstruct(): void
    {
        foreach ([
            'true' => true,
            'false' => false
        ] as $info => $var) {
            $varDumper = $this->getVarDumper($var);
            $processor = new BooleanProcessor($varDumper);
            $this->assertSame($info, $processor->info(), 'info');
            $processor->write();
            $this->assertSame(
                "boolean $info",
                $varDumper->writer()->toString(),
            );
        }
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new BooleanProcessor($this->getVarDumper(null));
    }
}
