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
use Chevere\Components\VarDump\Tests\Processors\Traits\VarProcessTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class NullProcessorTest extends TestCase
{
    use VarProcessTrait;

    public function testConstruct(): void
    {
        $var = null;
        $processor = new NullProcessor($this->getVarProcess($var));
        $this->assertSame('', $processor->info());
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NullProcessor($this->getVarProcess(''));
    }
}
