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
use Chevere\Components\X\Tests\AbstractProcessorTest;

final class NullProcessorTest extends AbstractProcessorTest
{
    protected function getProcessorName(): string
    {
        return NullProcessor::class;
    }

    protected function getInvalidConstructArgument()
    {
        return true;
    }

    public function testConstruct(): void
    {
        $var = null;
        $processor = new NullProcessor($this->getVarFormat($var));
        $this->assertSame('', $processor->info());
        $this->assertSame('', $processor->value());
    }
}
