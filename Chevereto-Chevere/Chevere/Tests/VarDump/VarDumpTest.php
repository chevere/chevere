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

namespace Chevere\Tests\VarDump;

use BadMethodCallException;
use Chevere\Components\Route\PathUri;
use Chevere\Components\VarDump\Contracts\VarDumpContract;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\HtmlDumper;
use Chevere\Components\VarDump\VarDump;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarDumpTest extends TestCase
{
    public function getVarDump(): VarDumpContract
    {
        return
            new VarDump(new PlainFormatter());
    }

    public function testConstruct(): void
    {
        $formatter = new PlainFormatter();
        $varDump = new VarDump($formatter);
        $this->assertSame($formatter, $varDump->formatter());
        $this->assertFalse($varDump->hasVar());
        $this->assertSame(null, $varDump->var());
        $this->assertSame([], $varDump->dontDump());
        $this->assertSame(0, $varDump->indent());
        $this->assertSame(0, $varDump->depth());
        $this->assertSame('', $varDump->indentString());
        $this->assertSame('', $varDump->toString());
        $this->expectException(BadMethodCallException::class);
        $varDump->process();
    }

    public function testWithDontDump(): void
    {
        $dontDump = ['ClassName1', 'ClassName2'];
        $varDump = $this->getVarDump()
            ->withDontDump(...$dontDump);
        $this->assertSame($dontDump, $varDump->dontDump());
    }

    public function testWithVar(): void
    {
        $var = 'some var';
        $varDump = $this->getVarDump()
            ->withVar($var);
        $this->assertSame($var, $varDump->var());
    }

    public function testWithIndent(): void
    {
        $indent = 10001;
        $varDump = $this->getVarDump()
            ->withIndent($indent);
        $this->assertSame($indent, $varDump->indent());
        $this->assertTrue($indent <= strlen($varDump->indentString()));
    }

    public function testWithDepth(): void
    {
        $depth = 4;
        $varDump = $this->getVarDump()
            ->withDepth($depth);
        $this->assertSame($depth, $varDump->depth());
    }

    public function testProcessSimpleTypes(): void
    {
        $types = [
            [null, 'null'],
            [true, 'boolean true'],
            [1, 'integer 1 (length=1)'],
            ['', 'string (length=0)'],
            [[], 'array (size=0)'],
            [new stdClass, 'object stdClass']
        ];
        foreach ($types as $values) {
            $varDump = $this->getVarDump()
                ->withVar($values[0])
                ->process();
            $this->assertSame($values[1], $varDump->toString());
        }
    }
}
