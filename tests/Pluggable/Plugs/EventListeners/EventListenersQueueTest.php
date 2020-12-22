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

namespace Chevere\Tests\Pluggable\Plugs\EventListeners;

use Chevere\Components\Pluggable\Plugs\EventListeners\EventListenersQueue;
use Chevere\Components\Pluggable\Types\EventListenerPlugType;
use Chevere\Interfaces\Pluggable\Plugs\EventListener\EventListenerInterface;
use Chevere\Tests\Pluggable\Plugs\EventListeners\_resources\TestEventListener;
use PHPUnit\Framework\TestCase;

final class EventListenersQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $eventListenersQueue = new EventListenersQueue();
        $this->assertSame($eventListenersQueue->interface(), EventListenerInterface::class);
        $this->assertInstanceOf(EventListenerPlugType::class, $eventListenersQueue->getPlugType());
    }

    public function testWithAddedEventListener(): void
    {
        $eventListener = new TestEventListener();
        $eventListenersQueue = (new EventListenersQueue())
            ->withAdded($eventListener);
        $this->assertSame([
            $eventListener->anchor() => [
                [
                    get_class($eventListener),
                ],
            ],
        ], $eventListenersQueue->plugsQueue()->toArray());
    }
}
