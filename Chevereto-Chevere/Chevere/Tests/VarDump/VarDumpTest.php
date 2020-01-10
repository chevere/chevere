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

use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\VarDump;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarDumpTest extends TestCase
{
    public function testConstruct(): void
    {
        $formatter = new PlainFormatter();
        $varDump = new VarDump($formatter);
        $this->assertSame($formatter, $varDump->formatter());
        $this->assertSame(null, $varDump->var());
        $this->assertSame([], $varDump->dontDump());
        $this->assertSame(0, $varDump->indent());
        $this->assertSame(0, $varDump->depth());
        $this->assertSame('', $varDump->indentString());
        $this->assertSame('', $varDump->toString());
        $varDump->process();
        $this->assertSame('null', $varDump->toString());
    }

    public function testSimpleTypes(): void
    {
        $types = [
            [null, 'null'],
            [true, 'boolean TRUE'],
            [1, 'integer 1 (length=1)'],
            ['', 'string (length=0)'],
            [[], 'array (size=0)'],
            [new stdClass, 'object stdClass']
        ];
        foreach ($types as $values) {
            $varDump = new VarDump(new PlainFormatter());
            $varDump = $varDump
                ->withVar($values[0])
                ->process();
            $this->assertSame($values[1], $varDump->toString());
        }
    }
}
