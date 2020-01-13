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

use Chevere\Components\VarDump\Processors\ObjectProcessor;
use Chevere\Components\X\Tests\AbstractProcessorTest;
use stdClass;

final class ObjectProcessorTest extends AbstractProcessorTest
{
    protected function getProcessorName(): string
    {
        return ObjectProcessor::class;
    }

    protected function getInvalidConstructArgument()
    {
        return [];
    }

    public function testConstruct(): void
    {
        $var = new stdClass;
        $var->prop = new stdClass;
        $className = 'stdClass';
        $processor = new ObjectProcessor($this->getVarDump($var));
        $this->assertSame($className, $processor->info());
        $this->assertStringContainsString('public $prop object stdClass', $processor->val());
    }
}
