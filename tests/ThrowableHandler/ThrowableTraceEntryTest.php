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

use Chevere\Components\ThrowableHandler\ThrowableTraceEntry;
use Chevere\Interfaces\VarDump\VarDumperInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ThrowableTraceEntryTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ThrowableTraceEntry([]);
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
        $traceEntry = new ThrowableTraceEntry($entry);
        $strings = [
            'file',
            'function',
            'class',
            'type',
        ];
        foreach ($strings as $method) {
            $this->assertSame('', $traceEntry->{$method}());
        }
        $this->assertSame([], $traceEntry->args());
        $this->assertSame(0, $traceEntry->line());
        $this->assertSame('', $traceEntry->fileLine());
    }

    public function testConstructTypes(): void
    {
        $filename = __FILE__;
        $line = 100;
        $entry = [
            'file' => $filename,
            'line' => $line,
            'function' => __FUNCTION__,
            'class' => __CLASS__,
            'type' => '->',
            'args' => [1, '2'],
        ];
        $traceEntry = new ThrowableTraceEntry($entry);
        foreach ($entry as $method => $val) {
            $this->assertSame($val, $traceEntry->{$method}());
        }
        $this->assertSame($line, $traceEntry->line());
        $this->assertSame($filename . ':' . $line, $traceEntry->fileLine());
    }

    public function testAnonClass(): void
    {
        $line = __LINE__ + 1;
        $fileLine = __FILE__ . ':' . strval($line);
        $entry = [
            'file' => null,
            'line' => null,
            'function' => 'method',
            'class' => VarDumperInterface::CLASS_ANON . $fileLine . '$a3',
            'type' => '->',
            'args' => [],
        ];
        $traceEntry = new ThrowableTraceEntry($entry);
        $this->assertSame(
            VarDumperInterface::CLASS_ANON,
            $traceEntry->class()
        );
        $this->assertSame($line, $traceEntry->line());
        $this->assertSame($fileLine, $traceEntry->fileLine());
    }

    public function testMissingAnonClassFile(): void
    {
        $line = 100;
        $anonPath = '/path/to/file.php';
        $anonFileLine = $anonPath . ':' . strval($line);
        $entry = [
            'file' => null,
            'line' => null,
            'function' => __FUNCTION__,
            'class' => 'class@anonymous' . $anonFileLine . '$b5',
            'type' => '->',
        ];
        $traceEntry = new ThrowableTraceEntry($entry);
        $this->assertSame($line, $traceEntry->line());
        $this->assertSame($anonFileLine, $traceEntry->fileLine());
    }

    public function testMissingClassFile(): void
    {
        $line = __LINE__ - 2;
        $entry = [
            'file' => null,
            'line' => null,
            'function' => __FUNCTION__,
            'class' => __CLASS__,
            'type' => '->',
            'args' => [1, '2'],
        ];
        $traceEntry = new ThrowableTraceEntry($entry);
        $this->assertSame($line, $traceEntry->line());
        $this->assertSame(__FILE__ . ':' . $line, $traceEntry->fileLine());
    }

    public function testMissingFileLine(): void
    {
        $entry = [
            'file' => 'duh',
            'line' => null,
            'function' => '',
            'class' => '',
            'type' => '->',
        ];
        $traceEntry = new ThrowableTraceEntry($entry);
        $this->assertSame(0, $traceEntry->line());
    }
}
