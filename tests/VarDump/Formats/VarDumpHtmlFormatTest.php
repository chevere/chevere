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

namespace Chevere\Tests\VarDump\Formats;

use Chevere\VarDump\Formats\VarDumpHtmlFormat;
use Chevere\VarDump\Interfaces\VarDumpHighlightInterface;
use PHPUnit\Framework\TestCase;

final class VarDumpHtmlFormatTest extends TestCase
{
    public function testIndent(): void
    {
        $baseIndent = strip_tags(VarDumpHtmlFormat::HTML_INLINE_PREFIX);
        $indent = 5;
        $indented = (new VarDumpHtmlFormat())->indent($indent);
        $stripped = strip_tags($indented);
        $expected = str_repeat($baseIndent, $indent);
        $this->assertSame($expected, $stripped);
    }

    public function testEmphasis(): void
    {
        $string = 'string';
        $emphasized = (new VarDumpHtmlFormat())->emphasis($string);
        $this->assertTrue(strlen($emphasized) > strlen($string));
    }

    public function testFilterEncodedChars(): void
    {
        $string = 'string</a>';
        $filtered = (new VarDumpHtmlFormat())->filterEncodedChars($string);
        $this->assertTrue(strlen($filtered) > strlen($string));
    }

    public function testHighlight(): void
    {
        $string = 'string';
        $highlighted = (new VarDumpHtmlFormat())->highlight(VarDumpHighlightInterface::KEYS[0], $string);
        $this->assertTrue(strlen($highlighted) > strlen($string));
    }
}
