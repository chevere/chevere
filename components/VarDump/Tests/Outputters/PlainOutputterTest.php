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

use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Outputters\ConsoleOutputter;
use Chevere\Components\VarDump\Outputters\PlainOutputter;
use Chevere\Components\VarDump\Tests\Traits\DebugBacktraceTrait;
use Chevere\Components\VarDump\Tests\Traits\VarDumperTrait;
use Chevere\Components\VarDump\VarDumper;
use Chevere\Components\Writers\StreamWriter;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\stream_for;

final class PlainOutputterTest extends TestCase
{
    use DebugBacktraceTrait;

    public function testNull(): void
    {
        $backtrace = $this->getDebugBacktrace();
        $writer = new StreamWriter(stream_for(''));
        $outputter = new PlainOutputter(
            $writer,
            $backtrace,
            new PlainFormatter,
            null,
        );
        $outputter->process();
        $this->assertSame(
            $backtrace[1]['class'] . '->' . $backtrace[1]['function']
            . "()\n" . $backtrace[0]['file'] . ':' . $backtrace[0]['line']
            . "\n\nArg#1 null",
            $writer->toString()
        );
    }
}
