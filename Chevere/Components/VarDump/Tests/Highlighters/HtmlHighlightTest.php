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

namespace Chevere\Tests\VarDump\Wrappers;

use Chevere\Components\VarDump\Interfaces\HighlightInterface;
use Chevere\Components\VarDump\Highlighters\HtmlHighlight;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class HtmlHighlightTest extends TestCase
{
    public function testInvalidArgumentConstruct(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new HtmlHighlight('invalid-argument');
    }

    public function testConstruct(): void
    {
        $dump = 'string';
        foreach (HighlightInterface::KEYS as $key) {
            $wrapper = new HtmlHighlight($key);
            $wrapped = $wrapper->wrap($dump);
            $this->assertTrue(strlen($wrapped) > strlen($dump));
        }
    }
}
