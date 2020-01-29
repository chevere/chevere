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
use Chevere\Components\VarDump\Interfaces\HighlightInterface;
use PHPUnit\Framework\TestCase;

final class HtmlFormatterTest extends TestCase
{
    public function testIndent(): void
    {
        $indent = 5;
        $indented = (new HtmlFormatter)->indent($indent);
        $this->assertTrue(strlen($indented) > $indent);
    }

    public function testEmphasis(): void
    {
        $string = 'string';
        $emphasized = (new HtmlFormatter)->emphasis($string);
        $this->assertTrue(strlen($emphasized) > $string);
    }

    public function testFilterEncodedChars(): void
    {
        $string = 'string</a>';
        $filtered = (new HtmlFormatter)->filterEncodedChars($string);
        $this->assertTrue(strlen($filtered) > $string);
    }

    public function testHighlight(): void
    {
        $string = 'string';
        $highlighted = (new HtmlFormatter)->highlight(HighlightInterface::KEYS[0], $string);
        $this->assertTrue(strlen($highlighted) > $string);
    }
}
