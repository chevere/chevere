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

use Chevere\Components\VarDump\Processors\ResourceProcessor;
use Chevere\Components\X\Tests\AbstractProcessorTest;

final class ResourceProcessorTest extends AbstractProcessorTest
{
    protected function getProcessorName(): string
    {
        return ResourceProcessor::class;
    }

    protected function getInvalidConstructArgument()
    {
        return false;
    }

    public function testConstruct(): void
    {
        $resource = fopen(__FILE__, 'r');
        $processor = new ResourceProcessor($this->getVarDump($resource));
        if (is_resource($resource)) {
            fclose($resource);
        }
        $this->assertSame('type=stream', $processor->info());
        $this->assertStringStartsWith('Resource id #', $processor->val());
    }
}
