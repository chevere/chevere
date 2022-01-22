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

namespace Chevere\Tests\VarDump\Highlights;

use Chevere\Components\VarDump\Highlights\VarDumpHtmlHighlight;
use Chevere\Exceptions\Core\OutOfRangeException;
use Chevere\Interfaces\VarDump\VarDumpHighlightInterface;
use PHPUnit\Framework\TestCase;

final class HtmlHighlightTest extends TestCase
{
    public function testInvalidArgumentConstruct(): void
    {
        $this->expectException(OutOfRangeException::class);
        new VarDumpHtmlHighlight('invalid-argument');
    }

    public function testConstruct(): void
    {
        $dump = 'string';
        foreach (VarDumpHighlightInterface::KEYS as $key) {
            $wrapper = new VarDumpHtmlHighlight($key);
            $wrapped = $wrapper->highlight($dump);
            $this->assertTrue(strlen($wrapped) > strlen($dump));
        }
    }
}
