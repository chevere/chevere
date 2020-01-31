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

use Chevere\Components\VarDump\Processors\FloatProcessor;
use Chevere\Components\VarDump\Tests\AbstractProcessorTest;

final class FloatProcessorTest extends AbstractProcessorTest
{
    protected function getProcessorName(): string
    {
        return FloatProcessor::class;
    }

    protected function getInvalidConstructArgument()
    {
        return 100;
    }

    public function testConstruct(): void
    {
        foreach ([0.1, 1.1, 10.0, 2.00, 110.011] as $var) {
            $stringVar = (string) $var;
            $processor = new FloatProcessor($this->getVarFormat($var));
            $this->assertSame('length=' . strlen($stringVar), $processor->info());
            $this->assertSame($stringVar, $processor->value());
        }
    }
}
