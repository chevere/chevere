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

use Chevere\Components\Hooks\Tests\_resources\TestHook;
use Chevere\Components\Plugs\PlugsQueue;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PluginsQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $hooksQueue = new PlugsQueue;
        $this->assertSame([], $hooksQueue->toArray());
    }

    public function testWithHook(): void
    {
        $hook = new TestHook;
        $pluginsQueue = new PlugsQueue;
        $pluginsQueue = $pluginsQueue->withPlug($hook);
        $this->assertSame([
            $hook->for() => [
                0 => [
                    get_class($hook)
                ]
            ]
        ], $pluginsQueue->toArray());
    }

    public function testWithAlreadyAddedHook(): void
    {
        $hook = new TestHook;
        $pluginsQueue = (new PlugsQueue)->withPlug($hook);
        $this->expectException(LogicException::class);
        $pluginsQueue->withPlug($hook);
    }
}
