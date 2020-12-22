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

namespace Chevere\Tests\ThrowableHandler\Formatters;

use Chevere\Components\Str\Str;
use Chevere\Components\ThrowableHandler\Formatters\ThrowableHandlerConsoleFormatter;
use Chevere\Components\ThrowableHandler\Formatters\ThrowableHandlerPlainFormatter;
use PHPUnit\Framework\TestCase;

final class ConsoleFormatterTest extends TestCase
{
    public function testConstruct(): void
    {
        $plainFormatter = new ThrowableHandlerPlainFormatter();
        $consoleFormatter = new ThrowableHandlerConsoleFormatter();

        $array = [
            'getTraceEntryTemplate' => [],
            'getHr' => [],
            'wrapLink' => ['value'],
            'wrapSectionTitle' => ['value'],
        ];
        foreach ($array as $methodName => $args) {
            $plain = $plainFormatter->{$methodName}(...$args);
            $console = $consoleFormatter->{$methodName}(...$args);
            $this->assertSame(
                $plain,
                (new Str($console))->withStripANSIColors()->toString()
            );
        }
    }
}
