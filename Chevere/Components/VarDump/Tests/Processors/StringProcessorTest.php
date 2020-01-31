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
use Chevere\Components\VarDump\Tests\AbstractProcessorTest;

final class StringProcessorTest extends AbstractProcessorTest
{
    protected function getProcessorName(): string
    {
        return StringProcessor::class;
    }

    protected function getInvalidConstructArgument()
    {
        return [];
    }

    public function testConstruct(): void
    {
        foreach (['', 'string', 'another string', '100', 'false'] as $var) {
            $processor = new StringProcessor($this->getVarFormat($var));
            $this->assertSame('length=' . strlen($var), $processor->info());
            $this->assertSame($var, $processor->value());
        }
    }
}
