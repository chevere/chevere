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

namespace Chevere\Tests\VarDump\Formatters;

use Chevere\Components\VarDump\Formatters\VarDumpConsoleFormatter;
use Chevere\Interfaces\VarDump\VarDumpHighlightInterface;
use PHPUnit\Framework\TestCase;

final class VarDumpConsoleFormatterTest extends TestCase
{
    public function testIndent(): void
    {
        $indent = 5;
        $indented = (new VarDumpConsoleFormatter())->indent($indent);
        $this->assertSame($indent, strlen($indented));
    }

    public function testEmphasis(): void
    {
        $string = 'string';
        $emphasized = (new VarDumpConsoleFormatter())->emphasis($string);
        $this->assertTrue(strlen($emphasized) >= strlen($string));
    }

    public function testFilterEncodedChars(): void
    {
        $string = 'string</a>';
        $filtered = (new VarDumpConsoleFormatter())->filterEncodedChars($string);
        $this->assertSame($string, $filtered);
    }

    public function testHighlight(): void
    {
        $string = 'string';
        $highlighted = (new VarDumpConsoleFormatter())
            ->highlight(VarDumpHighlightInterface::KEYS[0], $string);
        $this->assertTrue(strlen($highlighted) >= strlen($string));
    }
}
