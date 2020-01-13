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

use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use Chevere\Components\VarDump\Processors\ArrayProcessor;
use Chevere\Components\VarDump\Processors\BooleanProcessor;
use Chevere\Components\VarDump\Processors\FloatProcessor;
use Chevere\Components\VarDump\Processors\IntegerProcessor;
use Chevere\Components\VarDump\Processors\NullProcessor;
use Chevere\Components\VarDump\Processors\ObjectProcessor;
use Chevere\Components\VarDump\Processors\StringProcessor;
use Chevere\Components\VarDump\VarDump;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ArrayProcessorTest extends TestCase
{
    private function getVarDump($var): VarDumpInterface
    {
        return
            new VarDump(
                $var,
                new PlainFormatter()
            );
    }

    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ArrayProcessor($this->getVarDump(''));
    }

    public function testConstructEmpty(): void
    {
        $processor = new ArrayProcessor($this->getVarDump([]));
        $this->assertSame('size=0', $processor->info());
        $this->assertSame('', $processor->val());
    }

    public function testInt(): void
    {
        $var = 100;
        $stringVar = (string) $var;
        $processor = new IntegerProcessor($this->getVarDump($var));
        $this->assertSame('length=' . strlen($stringVar), $processor->info());
        $this->assertSame($stringVar, $processor->val());
    }

    public function testFloat(): void
    {
        $var = 1.1;
        $stringVar = (string) $var;
        $processor = new FloatProcessor($this->getVarDump($var));
        $this->assertSame('length=' . strlen($stringVar), $processor->info());
        $this->assertSame($stringVar, $processor->val());
    }

    public function testString(): void
    {
        $var = 'string';
        $processor = new StringProcessor($this->getVarDump($var));
        $this->assertSame('length=' . strlen($var), $processor->info());
        $this->assertSame($var, $processor->val());
    }

    public function testBoolean(): void
    {
        foreach ([
            'true' => true,
            'false' => false
        ] as $val => $var) {
            $processor = new BooleanProcessor($this->getVarDump($var));
            $this->assertSame('', $processor->info());
            $this->assertSame($val, $processor->val());
        }
    }

    public function testArray(): void
    {
        $var = [0, 1, 2, 3];
        $containTpl = '%s => integer %s (length=1)';
        $processor = new ArrayProcessor($this->getVarDump($var));
        $this->assertSame('size=' . count($var), $processor->info());
        foreach ($var as $int) {
            $this->assertStringContainsString(str_replace('%s', $int, $containTpl), $processor->val());
        }
    }

    public function testObject(): void
    {
        $var = new stdClass;
        $var->prop = new stdClass;
        $className = 'stdClass';
        $processor = new ObjectProcessor($this->getVarDump($var));
        $this->assertSame($className, $processor->info());
        $this->assertStringContainsString('public $prop object stdClass', $processor->val());
    }

    public function testNull(): void
    {
        $var = null;
        $processor = new NullProcessor($this->getVarDump($var));
        $this->assertSame('', $processor->info());
        $this->assertSame('', $processor->val());
    }
}
