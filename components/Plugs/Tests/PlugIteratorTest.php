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
use Chevere\Components\Plugs\PlugIterator;
use Chevere\Components\Plugs\Types\HookPlugType;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PlugIteratorTest extends TestCase
{
    public function testConstructInvalidDir(): void
    {
        $dir = new DirFromString(__DIR__ . '/' . uniqid() . '/');
        $this->expectException(LogicException::class);
        new PlugIterator($dir, new HookPlugType);
    }

    public function testConstruct(): void
    {
        $dir = (new DirFromString(__DIR__ . '/'))->getChild('_resources/PlugsIteratorTest/hooks/');
        $iterator = new PlugIterator($dir, new HookPlugType);
        $this->assertTrue(
            $iterator->plugsMapper()->map()
                ->hasKey('Chevere\Components\Plugs\Tests\_resources\PlugsIteratorTest\hookables\TestHookable')
        );
    }
}
