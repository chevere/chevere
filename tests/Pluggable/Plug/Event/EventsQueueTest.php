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

namespace Chevere\Tests\Pluggable\Plug\Event;

use Chevere\Pluggable\Interfaces\Plug\Event\EventInterface;
use Chevere\Pluggable\Plug\Event\EventsQueue;
use Chevere\Pluggable\Types\EventPlugType;
use Chevere\Tests\Pluggable\Plug\Event\_resources\TestEvent;
use PHPUnit\Framework\TestCase;

final class EventsQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $eventsQueue = new EventsQueue();
        $this->assertSame($eventsQueue->interface(), EventInterface::class);
        $this->assertInstanceOf(EventPlugType::class, $eventsQueue->getPlugType());
    }

    public function testWithAddedEvent(): void
    {
        $event = new TestEvent();
        $eventsQueue = (new EventsQueue())
            ->withAdded($event);
        $this->assertSame([
            $event->anchor() => [
                [
                    $event::class,
                ],
            ],
        ], $eventsQueue->plugsQueue()->toArray());
    }
}
