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
use Chevere\Components\VarDump\Interfaces\HighlightInterface;
use PHPUnit\Framework\TestCase;

final class PlainFormatterTest extends TestCase
{
    public function testIndent(): void
    {
        $indent = 5;
        $indented = (new PlainFormatter)->indent($indent);
        $this->assertTrue(strlen($indented) == $indent);
    }

    public function testEmphasis(): void
    {
        $string = 'string';
        $emphasized = (new PlainFormatter)->emphasis($string);
        $this->assertSame($string, $emphasized);
    }

    public function testFilterEncodedChars(): void
    {
        $string = 'string</a>';
        $filtered = (new PlainFormatter)->filterEncodedChars($string);
        $this->assertSame($string, $filtered);
    }

    public function testHighlight(): void
    {
        $string = 'string';
        $highlighted = (new PlainFormatter)->highlight(HighlightInterface::KEYS[0], $string);
        $this->assertSame($string, $highlighted);
    }
}
