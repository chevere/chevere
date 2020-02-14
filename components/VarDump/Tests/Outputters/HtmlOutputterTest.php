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

use Chevere\Components\VarDump\Formatters\HtmlFormatter;
use Chevere\Components\VarDump\Outputters\HtmlOutputter;
use Chevere\Components\VarDump\Tests\Traits\DebugBacktraceTrait;
use Chevere\Components\Writers\StreamWriter;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\stream_for;

final class HtmlOutputterTest extends TestCase
{
    use DebugBacktraceTrait;

    public function testNull(): void
    {
        $backtrace = $this->getDebugBacktrace();
        $writer = new StreamWriter(stream_for(''));
        (new HtmlOutputter(
            $writer,
            $backtrace,
            new HtmlFormatter,
            null,
        ))->process();
        $parsed = strtr(include '_resources/output-html.php', [
            '%handlerClassName%' => $backtrace[0]['class'],
            '%handlerFunctionName%' => $backtrace[0]['function'],
            '%fileLine%' => $backtrace[0]['file'] . ':' . $backtrace[0]['line'],
            '%className%' => $backtrace[1]['class'],
            '%functionName%' => $backtrace[1]['function'],
        ]);
        $this->assertSame($parsed, $writer->toString());
    }
}
