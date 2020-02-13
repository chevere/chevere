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

use stdClass;
use Chevere\Components\VarDump\Processors\BooleanProcessor;
use Chevere\Components\VarDump\Tests\Processors\Traits\VarProcessTrait;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BooleanProcessorTest extends TestCase
{
    use VarProcessTrait;

    protected function getInvalidConstructArgument()
    {
        return new stdClass;
    }

    public function testConstruct(): void
    {
        foreach ([
            'true' => true,
            'false' => false
        ] as $info => $var) {
            $processor = new BooleanProcessor($this->getVarProcess($var));
            $this->assertSame($info, $processor->info(), 'info');
        }
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new BooleanProcessor($this->getVarProcess(null));
    }
}
