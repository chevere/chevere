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

use Chevere\Components\VarDump\Formatters\ConsoleFormatter;
use Chevere\Components\VarDump\Interfaces\HighlightInterface;
use PHPUnit\Framework\TestCase;

final class ConsoleFormatterTest extends TestCase
{
    public function testIndent(): void
    {
        $indent = 5;
        $indented = (new ConsoleFormatter)->indent($indent);
        $this->assertTrue(strlen($indented) == $indent);
    }

    public function testEmphasis(): void
    {
        $string = 'string';
        $emphasized = (new ConsoleFormatter)->emphasis($string);
        $this->assertTrue(strlen($emphasized) >= $string);
    }

    public function testFilterEncodedChars(): void
    {
        $string = 'string</a>';
        $filtered = (new ConsoleFormatter)->filterEncodedChars($string);
        $this->assertSame($string, $filtered);
    }

    public function testHighlight(): void
    {
        $string = 'string';
        $highlighted = (new ConsoleFormatter)->highlight(HighlightInterface::KEYS[0], $string);
        $this->assertTrue(strlen($highlighted) >= $string);
    }
}
