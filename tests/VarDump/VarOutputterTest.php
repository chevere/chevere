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
use Chevere\Components\VarDump\Formatters\VarDumpConsoleFormatter;
use Chevere\Components\VarDump\Formatters\VarDumpHtmlFormatter;
use Chevere\Components\VarDump\Formatters\VarDumpPlainFormatter;
use Chevere\Components\VarDump\Outputters\VarDumpConsoleOutputter;
use Chevere\Components\VarDump\Outputters\VarDumpHtmlOutputter;
use Chevere\Components\VarDump\Outputters\VarDumpPlainOutputter;
use Chevere\Components\VarDump\VarOutputter;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Tests\VarDump\Traits\DebugBacktraceTrait;
use Laminas\Diactoros\StreamFactory;
use PHPUnit\Framework\TestCase;

final class VarOutputterTest extends TestCase
{
    use DebugBacktraceTrait;

    public function testPlainOutputter(): void
    {
        $backtrace = $this->getDebugBacktrace();
        $writer = new StreamWriter((new StreamFactory())->createStream(''));
        $varOutputter = new VarOutputter(
            $writer,
            $backtrace,
            new VarDumpPlainFormatter(),
            null,
        );
        $varOutputter->process(new VarDumpPlainOutputter());
        $this->assertSame($this->getParsed($backtrace, 'output-plain'), $writer->toString());
    }

    public function testConsoleOutputter(): void
    {
        $backtrace = $this->getDebugBacktrace();
        $writer = new StreamWriter((new StreamFactory())->createStream(''));
        $varOutputter = new VarOutputter(
            $writer,
            $backtrace,
            new VarDumpConsoleFormatter(),
            null,
        );
        $varOutputter->process(new VarDumpConsoleOutputter());
        $parsed = $this->getParsed($backtrace, 'output-console-color');
        $string = $writer->toString();
        $parsed = (new Str($parsed))->withStripANSIColors()->toString();
        $string = (new Str($string))->withStripANSIColors()->toString();
        $this->assertSame($parsed, $string);
    }

    public function testHtmlOutputter(): void
    {
        $backtrace = $this->getDebugBacktrace();
        $writer = new StreamWriter((new StreamFactory())->createStream(''));
        $varOutputter = new VarOutputter(
            $writer,
            $backtrace,
            new VarDumpHtmlFormatter(),
            null,
        );
        $varOutputter->process(new VarDumpHtmlOutputter());
        $parsed = $this->getParsed($backtrace, 'output-html');

        $this->assertSame($parsed, $writer->toString());
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
