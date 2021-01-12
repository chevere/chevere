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

use Chevere\Components\VarDump\Processors\VarDumpArrayProcessor;
use Chevere\Components\VarDump\Processors\VarDumpBooleanProcessor;
use Chevere\Components\VarDump\Processors\VarDumpFloatProcessor;
use Chevere\Components\VarDump\Processors\VarDumpIntegerProcessor;
use Chevere\Components\VarDump\Processors\VarDumpNullProcessor;
use Chevere\Components\VarDump\Processors\VarDumpObjectProcessor;
use Chevere\Components\VarDump\Processors\VarDumpResourceProcessor;
use Chevere\Components\VarDump\Processors\VarDumpStringProcessor;
use Chevere\Components\VarDump\VarDumpable;
use Chevere\Interfaces\Type\TypeInterface;
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
            TypeInterface::BOOL => [
                true, VarDumpBooleanProcessor::class,
            ],
            TypeInterface::FLOAT => [
                1.1, VarDumpFloatProcessor::class,
            ],
            TypeInterface::INT => [
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
