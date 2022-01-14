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

use Chevere\Components\VarDump\Formatters\VarDumpHtmlFormatter;
use Chevere\Interfaces\VarDump\VarDumpHighlightInterface;
use PHPUnit\Framework\TestCase;

final class VarDumpHtmlFormatterTest extends TestCase
{
    public function testIndent(): void
    {
        $baseIndent = strip_tags(VarDumpHtmlFormatter::HTML_INLINE_PREFIX);
        $indent = 5;
        $indented = (new VarDumpHtmlFormatter())->indent($indent);
        $stripped = strip_tags($indented);
        $expected = str_repeat($baseIndent, $indent);
        $this->assertSame($expected, $stripped);
    }

    public function testEmphasis(): void
    {
        $string = 'string';
        $emphasized = (new VarDumpHtmlFormatter())->emphasis($string);
        $this->assertTrue(strlen($emphasized) > strlen($string));
    }

    public function testFilterEncodedChars(): void
    {
        $string = 'string</a>';
        $filtered = (new VarDumpHtmlFormatter())->filterEncodedChars($string);
        $this->assertTrue(strlen($filtered) > strlen($string));
    }

    public function testHighlight(): void
    {
        $string = 'string';
        $highlighted = (new VarDumpHtmlFormatter())->highlight(VarDumpHighlightInterface::KEYS[0], $string);
        $this->assertTrue(strlen($highlighted) > strlen($string));
    }
}
