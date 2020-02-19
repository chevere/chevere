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

namespace Chevere\Components\ExceptionHandler\Tests;

use Chevere\Components\ExceptionHandler\Formatters\PlainFormatter;
use Chevere\Components\ExceptionHandler\TraceFormatter;
use Exception;
use PHPUnit\Framework\TestCase;

final class TraceFormatterTest extends TestCase
{
    public function testRealStackTrace(): void
    {
        $e = new Exception('Message', 100);
        $trace = new TraceFormatter($e->getTrace(), new PlainFormatter());
        $this->assertIsArray($trace->toArray());
        $this->assertIsString($trace->toString());
    }

    public function testFakeStackTrace(): void
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
        $trace = new TraceFormatter($trace, new PlainFormatter());
        $this->assertIsArray($trace->toArray());
        $this->assertIsString($trace->toString());
    }
}
