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

namespace Chevere\Tests\ThrowableHandler;

use Chevere\Components\ThrowableHandler\Formatters\ThrowableHandlerPlainFormatter;
use Chevere\Components\ThrowableHandler\ThrowableTraceFormatter;
use Exception;
use PHPUnit\Framework\TestCase;

final class ThrowableTraceFormatterTest extends TestCase
{
    private string $hrLine;

    protected function setUp(): void
    {
        $this->hrLine = str_repeat('-', 60);
    }

    public function testRealStackTrace(): void
    {
        $e = new Exception('Message', 100);
        $trace = new ThrowableTraceFormatter($e->getTrace(), new ThrowableHandlerPlainFormatter());
        $this->assertIsArray($trace->toArray());
        $this->assertIsString($trace->__toString());
    }

    public function testNullStackTrace(): void
    {
        $trace = [
            0 => [
                'file' => null,
                'line' => null,
                'function' => null,
                'class' => null,
                'type' => null,
                'args' => [false, null],
            ],
        ];
        $traceFormatter = new ThrowableTraceFormatter(
            $trace,
            new ThrowableHandlerPlainFormatter()
        );
        $this->assertSame([
            0 => "#0 \n(boolean false, NULL)",
        ], $traceFormatter->toArray());
        $this->assertSame(
            $this->hrLine .
            "\n#0 " .
            "\n(boolean false, NULL)" .
            "\n" . $this->hrLine,
            $traceFormatter->__toString()
        );
    }

    public function testFakeStackTrace(): void
    {
        $file = __FILE__;
        $line = 123;
        $fqn = 'The\\Full\\className';
        $type = '->';
        $method = 'methodName';
        $trace = [
            0 => [
                'file' => $file,
                'line' => $line,
                'function' => $method,
                'class' => $fqn,
                'type' => $type,
                'args' => [],
            ],
            1 => [
                'file' => $file,
                'line' => $line,
                'function' => $method,
                'class' => $fqn,
                'type' => $type,
                'args' => [],
            ],
        ];
        $traceFormatter = new ThrowableTraceFormatter(
            $trace,
            new ThrowableHandlerPlainFormatter()
        );
        $expectEntries = [];
        foreach (array_keys($trace) as $pos) {
            $expect = "#{$pos} ${file}:${line}\n${fqn}${type}${method}()";
            $expectEntries[] = $expect;
            $this->assertSame(
                $expect,
                $traceFormatter->toArray()[$pos]
            );
        }
        $expectString = $this->hrLine . "\n" .
            implode("\n" . $this->hrLine . "\n", $expectEntries) . "\n" .
            $this->hrLine;
        $this->assertSame($expectString, $traceFormatter->__toString());
    }
}
