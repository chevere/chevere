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

namespace Chevere\Tests\VarDump\Highlight;

use Chevere\Components\VarDump\Highlight\VarDumpConsoleHighlight;
use Chevere\Exceptions\Core\OutOfRangeException;
use Chevere\Interfaces\VarDump\VarDumpHighlightInterface;
use PHPUnit\Framework\TestCase;

final class ConsoleHighlightTest extends TestCase
{
    public function testInvalidArgumentConstruct(): void
    {
        $this->expectException(OutOfRangeException::class);
        new VarDumpConsoleHighlight('invalid-argument');
    }

    public function testConstruct(): void
    {
        $dump = 'string';
        $weas = [];
        foreach (VarDumpHighlightInterface::KEYS as $key) {
            $highlight = new VarDumpConsoleHighlight($key);
            $wrapped = $highlight->highlight($dump);
            $weas[] = [strlen($wrapped), strlen($dump)];
            // >= because the target console may not support color
            $this->assertTrue(strlen($wrapped) >= strlen($dump));
        }
    }
}
