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
use Chevere\Components\Plugs\Tests\_resources\TypedPlugsQueueTests\TestTypedPlugsQueueInvalidAccept;
use Chevere\Components\Plugs\Types\EventListenerPlugType;
use PHPUnit\Framework\TestCase;

final class TypedPlugsQueueTest extends TestCase
{
    public function testBadTypedQueue(): void
    {
        $plugsQueue = new PlugsQueue(new EventListenerPlugType);
        $this->expectException(PlugInterfaceException::class);
        new TestTypedPlugsQueueInvalidAccept($plugsQueue);
    }

    // public function testHooksQueue(): void
    // {
    //     $plugsQueue = new PlugsQueue(new HookPlugType);
    //     $this->expectNotToPerformAssertions();
    //     new HooksQueue($plugsQueue);
    // }

    // public function testEventListenersQueue(): void
    // {
    //     $plugsQueue = new PlugsQueue(new EventListenerPlugType);
    //     $this->expectNotToPerformAssertions();
    //     new EventListenersQueue($plugsQueue);
    // }
}
