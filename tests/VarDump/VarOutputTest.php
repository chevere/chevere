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

use Chevere\Components\Str\Str;
use Chevere\Components\VarDump\Format\VarDumpConsoleFormat;
use Chevere\Components\VarDump\Format\VarDumpHtmlFormat;
use Chevere\Components\VarDump\Format\VarDumpPlainFormat;
use Chevere\Components\VarDump\Output\VarDumpConsoleOutput;
use Chevere\Components\VarDump\Output\VarDumpHtmlOutput;
use Chevere\Components\VarDump\Output\VarDumpPlainOutput;
use Chevere\Components\VarDump\VarOutput;
use function Chevere\Components\Writer\streamTemp;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Tests\VarDump\Traits\DebugBacktraceTrait;
use PHPUnit\Framework\TestCase;

final class VarOutputTest extends TestCase
{
    use DebugBacktraceTrait;

    public function testPlainOutputter(): void
    {
        $backtrace = $this->getDebugBacktrace();
        $writer = new StreamWriter(streamTemp(''));
        $varOutputter = new VarOutput(
            writer: $writer,
            backtrace: $backtrace,
            format: new VarDumpPlainFormat()
        );
        $varOutputter->process(
            new VarDumpPlainOutput(),
            name: null,
            id: 123
        );
        $this->assertSame(
            $this->getParsed($backtrace, 'output-plain'),
            $writer->__toString(),
        );
    }

    public function testConsoleOutputter(): void
    {
        $backtrace = $this->getDebugBacktrace();
        $writer = new StreamWriter(streamTemp(''));
        $varOutputter = new VarOutput(
            writer: $writer,
            backtrace: $backtrace,
            format: new VarDumpConsoleFormat(),
        );
        $varOutputter->process(new VarDumpConsoleOutput(), name: null);
        $parsed = $this->getParsed($backtrace, 'output-console-color');
        $string = $writer->__toString();
        $parsed = (new Str($parsed))->withStripANSIColors()->__toString();
        $string = (new Str($string))->withStripANSIColors()->__toString();
        $this->assertSame($parsed, $string);
    }

    public function testHtmlOutputter(): void
    {
        $backtrace = $this->getDebugBacktrace();
        $writer = new StreamWriter(streamTemp(''));
        $varOutputter = new VarOutput(
            writer: $writer,
            backtrace: $backtrace,
            format: new VarDumpHtmlFormat(),
        );
        $varOutputter->process(new VarDumpHtmlOutput(), name: null);
        $parsed = $this->getParsed($backtrace, 'output-html');
        $this->assertSame($parsed, $writer->__toString());
    }

    private function getParsed(array $backtrace, string $name): string
    {
        return strtr(include "_resources/${name}.php", [
            '%handlerClassName%' => $backtrace[0]['class'],
            '%handlerFunctionName%' => $backtrace[0]['function'],
            '%fileLine%' => $backtrace[0]['file'] . ':' . $backtrace[0]['line'],
            '%className%' => $backtrace[1]['class'],
            '%functionName%' => $backtrace[1]['function'],
        ]);
    }
}
