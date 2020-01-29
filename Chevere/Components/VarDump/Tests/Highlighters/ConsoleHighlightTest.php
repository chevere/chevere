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
use Chevere\Components\VarDump\Highlighters\ConsoleHighlight;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ConsoleHighlightTest extends TestCase
{
    public function testInvalidArgumentConstruct(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ConsoleHighlight('invalid-argument');
    }

    public function testConstruct(): void
    {
        $dump = 'string';
        $weas = [];
        foreach (HighlightInterface::KEYS as $key) {
            $highlight = new ConsoleHighlight($key);
            $wrapped = $highlight->wrap($dump);
            $weas[] = [strlen($wrapped), strlen($dump)];
            // >= because the target console may not support color
            $this->assertTrue(strlen($wrapped) >= strlen($dump));
        }
    }
}
