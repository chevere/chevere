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

use Chevere\Components\VarDump\Formats\VarDumpPlainFormat;
use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\VarDumpable;
use Chevere\Components\VarDump\VarDumper;
use function Chevere\Components\Writer\streamTemp;
use Chevere\Components\Writer\StreamWriter;
use Ds\Set;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarDumperTest extends TestCase
{
    public function testConstruct(): void
    {
        $var = ['foo', new stdClass()];
        $defaultIndent = 0;
        $defaultDepth = 0;
        $defaultIndentSting = '';
        $writer = new StreamWriter(streamTemp(''));
        $format = new VarDumpPlainFormat();
        $dumpable = new VarDumpable($var);
        $varDumper = new VarDumper(
            writer: $writer,
            format: $format,
            dumpable: $dumpable
        );
        $this->assertSame($writer, $varDumper->writer());
        $this->assertSame($format, $varDumper->format());
        $this->assertSame($dumpable, $varDumper->dumpable());
        $this->assertSame($defaultIndent, $varDumper->indent());
        $this->assertSame($defaultDepth, $varDumper->depth());
        $this->assertSame($defaultIndentSting, $varDumper->indentString());
        $this->assertCount(0, $varDumper->known());
        for ($integer = 1; $integer <= 5; $integer++) {
            $this->hookTestWithIndent($varDumper, $integer);
            $this->hookTestWithDepth($varDumper, $integer);
            $varDumperWithProcess = $this->hookTestWithProcess(
                $varDumperWithProcess ?? $varDumper,
                $integer
            );
        }
        $this->hookTestWithKnownObjects(
            $varDumper,
            new Set([new stdClass(), new stdClass()])
        );
    }

    public function hookTestWithIndent(VarDumperInterface $varDumper, int $indent): void
    {
        $varDumperWithIndent = $varDumper->withIndent($indent);
        $this->assertNotSame($varDumper, $varDumperWithIndent);
        $this->assertSame($indent, $varDumperWithIndent->indent());
        $this->assertSame(
            str_repeat(' ', $indent),
            $varDumperWithIndent->indentString()
        );
    }

    public function hookTestWithDepth(
        VarDumperInterface $varDumper,
        int $depth
    ): void {
        $varDumperWithDepth = $varDumper->withDepth($depth);
        $this->assertNotSame($varDumper, $varDumperWithDepth);
        $this->assertSame($depth, $varDumperWithDepth->depth());
    }

    public function hookTestWithKnownObjects(
        VarDumperInterface $varDumper,
        Set $known
    ): void {
        $varDumperWithObjects = $varDumper->withKnownObjects($known);
        $this->assertNotSame($varDumper, $varDumperWithObjects);
        $this->assertSame($known, $varDumperWithObjects->known());
    }

    public function hookTestWithProcess(
        VarDumperInterface $varDumper,
        int $indent
    ): VarDumperInterface {
        $varDumperWithProcess = $varDumper->withProcess();
        $this->assertNotSame($varDumper, $varDumperWithProcess);
        $this->assertSame($indent, $varDumperWithProcess->indent());

        return $varDumperWithProcess;
    }
}
