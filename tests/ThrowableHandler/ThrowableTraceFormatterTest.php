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
    public function testRealStackTrace(): void
    {
        $e = new Exception('Message', 100);
        $trace = new ThrowableTraceFormatter($e->getTrace(), new ThrowableHandlerPlainFormatter());
        $this->assertIsArray($trace->toArray());
        $this->assertIsString($trace->toString());
    }

    public function testEmptyStackTrace(): void
    {
        $trace = [
            0 => [
                'file' => null,
                'line' => null,
                'function' => null,
                'class' => null,
                'type' => null,
                'args' => [0, null]
            ]
        ];
        $trace = new ThrowableTraceFormatter($trace, new ThrowableHandlerPlainFormatter());
        $this->assertSame([0 => "#0 \n()"], $trace->toArray());
        $this->assertSame(
            "------------------------------------------------------------" . 
            "\n#0 " .
            "\n()" .
            "\n------------------------------------------------------------",
            $trace->toString()
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
                'args' => []
            ]
        ];
        $trace = new ThrowableTraceFormatter($trace, new ThrowableHandlerPlainFormatter());
        $this->assertSame(
            [0 => "#0 $file:$line\n$fqn$type$method()"],
            $trace->toArray()
        );
        $this->assertSame(
            "------------------------------------------------------------" .
            "\n#0 $file:$line" .
            "\n$fqn$type$method()" .
            "\n------------------------------------------------------------",
            $trace->toString()
        );
    }
}

