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

namespace Chevere\Components\VarDump\Tests;

use Chevere\Components\VarDump\VarDumpeable;
use InvalidArgumentException;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Interfaces\VarFormatInterface;
use Chevere\Components\VarDump\VarFormat;
use PHPUnit\Framework\TestCase;

abstract class AbstractProcessorTest extends TestCase
{
    final protected function getVarFormat($var): VarFormatInterface
    {
        return
            new VarFormat(
                new VarDumpeable($var),
                new PlainFormatter()
            );
    }

    final public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $processorName = $this->getProcessorName();
        new $processorName(
            $this->getVarFormat(
                $this->getInvalidConstructArgument()
            )
        );
    }

    abstract protected function getProcessorName(): string;

    abstract protected function getInvalidConstructArgument();
}
