<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump\Tests\Processors;

use stdClass;
use Chevere\Components\VarDump\Processors\BooleanProcessor;
use Chevere\Components\X\Tests\AbstractProcessorTest;

final class BooleanProcessorTest extends AbstractProcessorTest
{
    protected function getProcessorName(): string
    {
        return BooleanProcessor::class;
    }

    protected function getInvalidConstructArgument()
    {
        return new stdClass;
    }

    public function testConstruct(): void
    {
        foreach ([
            'true' => true,
            'false' => false
        ] as $val => $var) {
            $processor = new BooleanProcessor($this->getVarDump($var));
            $this->assertSame('', $processor->info());
            $this->assertSame($val, $processor->val());
        }
    }
}
