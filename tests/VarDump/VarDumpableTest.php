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

namespace Chevere\Tests\VarDump;

use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarDump\Processors\VarDumpArrayProcessor;
use Chevere\VarDump\Processors\VarDumpBooleanProcessor;
use Chevere\VarDump\Processors\VarDumpFloatProcessor;
use Chevere\VarDump\Processors\VarDumpIntegerProcessor;
use Chevere\VarDump\Processors\VarDumpNullProcessor;
use Chevere\VarDump\Processors\VarDumpObjectProcessor;
use Chevere\VarDump\Processors\VarDumpResourceProcessor;
use Chevere\VarDump\Processors\VarDumpStringProcessor;
use Chevere\VarDump\VarDumpable;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarDumpableTest extends TestCase
{
    public function testConstruct(): void
    {
        $variables = [
            TypeInterface::ARRAY => [
                [], VarDumpArrayProcessor::class,
            ],
            TypeInterface::BOOLEAN => [
                true, VarDumpBooleanProcessor::class,
            ],
            TypeInterface::FLOAT => [
                1.1, VarDumpFloatProcessor::class,
            ],
            TypeInterface::INTEGER => [
                1, VarDumpIntegerProcessor::class,
            ],
            TypeInterface::NULL => [
                null, VarDumpNullProcessor::class,
            ],
            TypeInterface::OBJECT => [
                new stdClass(), VarDumpObjectProcessor::class,
            ],
            TypeInterface::RESOURCE => [
                fopen(__FILE__, 'r'),
                VarDumpResourceProcessor::class,
            ],
            TypeInterface::STRING => [
                '',
                VarDumpStringProcessor::class,
            ],
        ];
        foreach ($variables as $type => $var) {
            $variableDump = new VarDumpable($var[0]);
            $this->assertSame($var[0], $variableDump->var());
            $this->assertSame($type, $variableDump->type());
        }
    }
}
