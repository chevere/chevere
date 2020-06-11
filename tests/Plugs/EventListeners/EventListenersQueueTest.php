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

namespace Chevere\Tests\Plugs\EventListeners;

use Chevere\Components\Plugin\PlugsQueue;
use Chevere\Components\Plugin\Types\EventListenerPlugType;
use Chevere\Components\Plugs\EventListeners\EventListenersQueue;
use Chevere\Interfaces\Plugs\EventListener\EventListenerInterface;
use PHPUnit\Framework\TestCase;

final class EventListenersQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $plugsQueue = new PlugsQueue(new EventListenerPlugType);
        $eventListenersQueue = new EventListenersQueue;
        $this->assertSame($eventListenersQueue->accept(), EventListenerInterface::class);
    }
}
