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

use Chevere\Components\VarDump\Exceptions\TypeException;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use Chevere\Components\VarDump\Processors\ArrayProcessor;
use Chevere\Components\VarDump\Processors\BooleanProcessor;
use Chevere\Components\VarDump\Processors\ObjectProcessor;
use Chevere\Components\VarDump\Processors\ScalarProcessor;
use Chevere\Components\VarDump\VarDump;
use phpDocumentor\Reflection\Types\Scalar;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ArrayProcessorTest extends TestCase
{
    public function getProcessors(): array
    {
        return [
            'ArrayProcessor' => [
                new ArrayProcessor(),
                [[]],
                'string',
            ],
            'BooleanProcessor' => [
                new BooleanProcessor(),
                [true],
                'string',
            ],
            'ObjectProcessor' => [
                new ObjectProcessor(),
                [new stdClass],
                'string',
            ],
            'ScalarProcessor' => [
                new ScalarProcessor(),
                [1, 1.1, 'string', null],
                [],
            ],
        ];
    }

    private function getVarDump(): VarDumpInterface
    {
        return
            new VarDump(
                new PlainFormatter()
            );
    }

    public function testConstruct(): void
    {
        // $this->expectException(TypeException::class);
        $processor = new ArrayProcessor($this->getVarDump());
        xdd($processor);
    }
}
