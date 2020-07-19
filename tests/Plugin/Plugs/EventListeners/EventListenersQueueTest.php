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

namespace Chevere\Tests\Plugin\Plugs\EventListeners;

use Chevere\Components\Plugin\Plugs\EventListeners\EventListenersQueue;
use Chevere\Components\Plugin\Types\EventListenerPlugType;
use Chevere\Interfaces\Plugin\Plugs\EventListener\EventListenerInterface;
use Chevere\Tests\Plugin\Plugs\EventListeners\_resources\TestEventListener;
use PHPUnit\Framework\TestCase;

final class EventListenersQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $eventListenersQueue = new EventListenersQueue;
        $this->assertSame($eventListenersQueue->interface(), EventListenerInterface::class);
        $this->assertEquals(new EventListenerPlugType, $eventListenersQueue->getPlugType());
    }

    public function testWithAddedEventListener(): void
    {
        $eventListener = new TestEventListener;
        $eventListenersQueue = (new EventListenersQueue)
            ->withAdded($eventListener);
        $this->assertSame([
            $eventListener->anchor() => [
                [
                    get_class($eventListener)
                ]
            ]
        ], $eventListenersQueue->plugsQueue()->toArray());
    }
}
