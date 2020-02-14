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

use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarDump\VarDumpeable;
use Chevere\Components\VarDump\Processors\ArrayProcessor;
use Chevere\Components\VarDump\Processors\BooleanProcessor;
use Chevere\Components\VarDump\Processors\FloatProcessor;
use Chevere\Components\VarDump\Processors\IntegerProcessor;
use Chevere\Components\VarDump\Processors\NullProcessor;
use Chevere\Components\VarDump\Processors\ObjectProcessor;
use Chevere\Components\VarDump\Processors\ResourceProcessor;
use Chevere\Components\VarDump\Processors\StringProcessor;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarDumpeableTest extends TestCase
{
    public function testConstruct(): void
    {
        $variables = [
            TypeInterface::ARRAY => [
                [], ArrayProcessor::class
            ],
            TypeInterface::BOOLEAN => [
                true, BooleanProcessor::class
            ],
            TypeInterface::FLOAT => [
                1.1, FloatProcessor::class
            ],
            TypeInterface::INTEGER => [
                1, IntegerProcessor::class
            ],
            TypeInterface::NULL => [
                null, NullProcessor::class
            ],
            TypeInterface::OBJECT => [
                new stdClass, ObjectProcessor::class
            ],
            TypeInterface::RESOURCE => [
                fopen(__FILE__, 'r'),
                ResourceProcessor::class
            ],
            TypeInterface::STRING => [
                '',
                StringProcessor::class
            ],
        ];
        foreach ($variables as $type => $var) {
            $variableDump = new VarDumpeable($var[0]);
            $this->assertSame($var[0], $variableDump->var());
            $this->assertSame($type, $variableDump->type());
        }
    }
}
