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

namespace Chevere\Components\Plugin\Tests;

use Chevere\Components\Plugin\Exceptions\PlugInterfaceException;
use Chevere\Components\Plugin\PlugsQueue;
use Chevere\Components\Plugin\Tests\_resources\TypedPlugsQueueTests\TestTypedPlugsQueueInvalidAccept;
use Chevere\Components\Plugin\Types\EventListenerPlugType;
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
