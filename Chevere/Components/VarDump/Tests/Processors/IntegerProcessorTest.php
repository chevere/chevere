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
use Chevere\Components\X\Tests\AbstractProcessorTest;

final class IntegerProcessorTest extends AbstractProcessorTest
{
    protected function getProcessorName(): string
    {
        return IntegerProcessor::class;
    }

    protected function getInvalidConstructArgument()
    {
        return 1.1;
    }

    public function testConstruct(): void
    {
        foreach ([0, 1, 100, 200, 110011] as $var) {
            $stringVar = (string) $var;
            $processor = new IntegerProcessor($this->getVarFormat($var));
            $this->assertSame('length=' . strlen($stringVar), $processor->info());
            $this->assertSame($stringVar, $processor->value());
        }
    }
}
