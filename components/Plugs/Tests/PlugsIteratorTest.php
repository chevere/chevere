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

namespace Chevere\Components\Plugs\Tests;

use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Plugs\PlugsIterator;
use Chevere\Components\Plugs\PlugTypeHook;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PlugsIteratorTest extends TestCase
{
    public function testConstructInvalidDir(): void
    {
        $dir = new DirFromString(__DIR__ . '/' . uniqid() . '/');
        $this->expectException(LogicException::class);
        new PlugsIterator($dir, new PlugTypeHook);
    }

    public function testConstruct(): void
    {
        $dir = (new DirFromString(__DIR__ . '/'))->getChild('_resources/PlugsIteratorTest/hooks/');
        $iterator = new PlugsIterator($dir, new PlugTypeHook);
        $this->assertTrue(
            $iterator->plugsMapper()->map()
                ->hasKey('Chevere\Components\Plugs\Tests\_resources\PlugsIteratorTest\hookables\TestHookable')
        );
    }
}
