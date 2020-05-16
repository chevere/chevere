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

use Chevere\Components\Plugs\Exceptions\PlugInterfaceException;
use Chevere\Components\Plugs\PlugsQueue;
use Chevere\Components\Plugs\Tests\_resources\src\TestHook;
use Chevere\Components\Plugs\Types\EventListenerPlugType;
use Chevere\Components\Plugs\Types\HookPlugType;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PlugsQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $plugType = new HookPlugType;
        $plugsQueue = new PlugsQueue($plugType);
        $this->assertSame($plugType, $plugsQueue->plugType());
        $this->assertSame([], $plugsQueue->toArray());
    }

    public function testWithWrongPlug(): void
    {
        $hook = new TestHook;
        $plugType = new EventListenerPlugType;
        $plugsQueue = new PlugsQueue($plugType);
        $this->expectException(PlugInterfaceException::class);
        $plugsQueue->withAddedPlug($hook);
    }

    public function testWithPlug(): void
    {
        $hook = new TestHook;
        $plugQueue = new PlugsQueue(new HookPlugType);
        $plugQueue = $plugQueue->withAddedPlug($hook);
        $this->assertSame([
            $hook->anchor() => [
                0 => [
                    get_class($hook)
                ]
            ]
        ], $plugQueue->toArray());
    }

    public function testWithAlreadyAddedPlug(): void
    {
        $hook = new TestHook;
        $plugsQueue = (new PlugsQueue(new HookPlugType))
            ->withAddedPlug($hook);
        $this->expectException(LogicException::class);
        $plugsQueue->withAddedPlug($hook);
    }
}
