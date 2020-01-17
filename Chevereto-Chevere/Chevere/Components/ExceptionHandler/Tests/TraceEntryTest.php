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

namespace Chevere\Components\ExceptionHandler\Tests;

use InvalidArgumentException;
use Chevere\Components\ExceptionHandler\TraceEntry;
use PHPUnit\Framework\TestCase;

final class TraceEntryTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TraceEntry([]);
    }

    public function testConstructInvalidTypes(): void
    {
        $entry = [
            'file' => null,
            'line' => null,
            'function' => null,
            'class' => null,
            'type' => null,
        ];
        $traceEntry = new TraceEntry($entry);
        $strings = [
            'file',
            'function',
            'class',
            'type',
        ];
        foreach ($strings as $method) {
            $this->assertSame('', $traceEntry->$method());
        }
        $this->assertSame([], $traceEntry->args());
        $this->assertSame(0, $traceEntry->line());
        $this->assertSame('', $traceEntry->fileLine());
    }

    public function testConstructTypes(): void
    {
        $entry = [
            'file' => __FILE__,
            'line' => 100,
            'function' => __FUNCTION__,
            'class' => __CLASS__,
            'type' => '->',
            'args' => [1, '2']
        ];
        $traceEntry = new TraceEntry($entry);
        foreach ($entry as $method => $val) {
            $this->assertSame($val, $traceEntry->$method());
        }
        $this->assertSame($entry['file'] . ':' . $entry['line'], $traceEntry->fileLine());
    }
}
