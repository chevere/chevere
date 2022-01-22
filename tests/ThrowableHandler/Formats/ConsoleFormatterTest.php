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

namespace Chevere\Tests\ThrowableHandler\Formats;

use Chevere\Components\Str\Str;
use Chevere\Components\ThrowableHandler\Formats\ThrowableHandlerConsoleFormat;
use Chevere\Components\ThrowableHandler\Formats\ThrowableHandlerPlainFormat;
use PHPUnit\Framework\TestCase;

final class ConsoleFormatterTest extends TestCase
{
    public function testConstruct(): void
    {
        $plainFormatter = new ThrowableHandlerPlainFormat();
        $consoleFormatter = new ThrowableHandlerConsoleFormat();
        $array = [
            'getTraceEntryTemplate' => [],
            'getHr' => [],
            'getLineBreak' => [],
            'wrapLink' => ['value'],
            'wrapSectionTitle' => ['value'],
            'wrapTitle' => ['value'],
        ];
        foreach ($array as $methodName => $args) {
            $plain = $plainFormatter->{$methodName}(...$args);
            $console = $consoleFormatter->{$methodName}(...$args);
            $this->assertSame(
                $plain,
                (new Str($console))->withStripANSIColors()->__toString()
            );
        }
    }
}
