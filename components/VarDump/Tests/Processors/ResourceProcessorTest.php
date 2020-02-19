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
use Chevere\Components\VarDump\Tests\Traits\VarDumperTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ResourceProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testConstruct(): void
    {
        $resource = fopen(__FILE__, 'r');
        if (is_resource($resource) === false) {
            $this->markTestIncomplete('Unable to fopen ' . __FILE__);
        }
        $resourceString = (string) $resource;
        $expectedInfo = 'type=' . get_resource_type($resource);
        $varDumper = $this->getVarDumper($resource);
        $processor = new ResourceProcessor($varDumper);
        $this->assertSame($expectedInfo, $processor->info());
        $processor->write();
        $this->assertSame(
            $resourceString . " ($expectedInfo)",
            $varDumper->writer()->toString()
        );
        if (is_resource($resource)) {
            fclose($resource);
        }
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ResourceProcessor($this->getVarDumper(null));
    }
}
