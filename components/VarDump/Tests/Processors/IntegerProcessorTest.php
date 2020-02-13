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

use Chevere\Components\VarDump\Processors\IntegerProcessor;
use Chevere\Components\VarDump\Tests\Processors\Traits\VarProcessTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class IntegerProcessorTest extends TestCase
{
    use VarProcessTrait;

    public function testConstruct(): void
    {
        foreach ([0, 1, 100, 200, 110011] as $var) {
            $stringVar = (string) $var;
            $processor = new IntegerProcessor($this->getVarProcess($var));
            $this->assertSame('length=' . strlen($stringVar), $processor->info());
        }
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new IntegerProcessor($this->getVarProcess(1.1));
    }
}
