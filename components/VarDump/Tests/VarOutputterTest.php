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

use Chevere\Components\Str\Str;
use Chevere\Components\VarDump\Formatters\ConsoleFormatter;
use Chevere\Components\VarDump\Formatters\HtmlFormatter;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Outputters\ConsoleOutputter;
use Chevere\Components\VarDump\Outputters\HtmlOutputter;
use Chevere\Components\VarDump\Outputters\PlainOutputter;
use Chevere\Components\VarDump\Tests\Traits\DebugBacktraceTrait;
use Chevere\Components\VarDump\VarOutputter;
use Chevere\Components\Writers\StreamWriter;
use JakubOnderka\PhpConsoleColor\ConsoleColor;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\stream_for;

final class VarOutputterTest extends TestCase
{
    use DebugBacktraceTrait;

    private function getParsed(array $backtrace, string $name): string
    {
        return strtr(include "_resources/$name.php", [
            '%handlerClassName%' => $backtrace[0]['class'],
            '%handlerFunctionName%' => $backtrace[0]['function'],
            '%fileLine%' => $backtrace[0]['file'] . ':' . $backtrace[0]['line'],
            '%className%' => $backtrace[1]['class'],
            '%functionName%' => $backtrace[1]['function'],
        ]);
    }

    public function testPlainOutputter(): void
    {
        $backtrace = $this->getDebugBacktrace();
        $writer = new StreamWriter(stream_for(''));
        $varOutputter = new VarOutputter(
            $writer,
            $backtrace,
            new PlainFormatter,
            null,
        );
        $varOutputter->process(new PlainOutputter);
        $this->assertSame($this->getParsed($backtrace, 'output-plain'), $writer->toString());
    }

    public function testConsoleOutputter(): void
    {
        $backtrace = $this->getDebugBacktrace();
        $writer = new StreamWriter(stream_for(''));
        $varOutputter = new VarOutputter(
            $writer,
            $backtrace,
            new ConsoleFormatter,
            null,
        );
        $varOutputter->process(new ConsoleOutputter);
        $parsed = $this->getParsed($backtrace, 'output-console-color');
        $string = $writer->toString();
        // if ((new ConsoleColor())->isSupported() === false) {
        // }
        $parsed = (string) (new Str($parsed))->stripANSIColors();
        $string = (string) (new Str($string))->stripANSIColors();
        $this->assertSame($parsed, $string);
    }

    public function testHtmlOutputter(): void
    {
        $backtrace = $this->getDebugBacktrace();
        $writer = new StreamWriter(stream_for(''));
        $varOutputter = new VarOutputter(
            $writer,
            $backtrace,
            new HtmlFormatter,
            null,
        );
        $varOutputter->process(new HtmlOutputter);
        $parsed = $this->getParsed($backtrace, 'output-html');
        $this->assertSame($parsed, $writer->toString());
    }
}
