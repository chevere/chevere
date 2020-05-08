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

namespace Chevere\Components\Hooks\Tests;

use Chevere\Components\Extend\PluginsQueue;
use Chevere\Components\Hooks\Tests\_resources\TestHook;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PluginsQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $hooksQueue = new PluginsQueue;
        $this->assertSame([], $hooksQueue->toArray());
    }

    public function testWithHook(): void
    {
        $hook = new TestHook;
        $pluginsQueue = new PluginsQueue;
        $pluginsQueue = $pluginsQueue->withPlugin($hook);
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
        $pluginsQueue = (new PluginsQueue)->withPlugin($hook);
        $this->expectException(LogicException::class);
        $pluginsQueue->withPlugin($hook);
    }
}
