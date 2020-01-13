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

namespace Chevere\Components\X\Tests;

use InvalidArgumentException;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use Chevere\Components\VarDump\VarDump;
use PHPUnit\Framework\TestCase;

abstract class AbstractProcessorTest extends TestCase
{
    final protected function getVarDump($var): VarDumpInterface
    {
        return
            new VarDump(
                $var,
                new PlainFormatter()
            );
    }

    final public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $processorName = $this->getProcessorName();
        new $processorName(
            $this->getVarDump(
                $this->getInvalidConstructArgument()
            )
        );
    }

    abstract protected function getProcessorName(): string;

    abstract protected function getInvalidConstructArgument();
}
