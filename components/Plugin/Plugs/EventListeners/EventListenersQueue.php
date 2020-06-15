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

namespace Chevere\Components\Plugin\Plugs\EventListeners;

use Chevere\Components\Plugin\Traits\TypedPlugsQueueTrait;
use Chevere\Components\Plugin\Types\EventListenerPlugType;
use Chevere\Interfaces\Plugin\PlugTypeInterface;
use Chevere\Interfaces\Plugin\Plugs\EventListener\EventListenerInterface;
use Chevere\Interfaces\Plugin\Plugs\EventListener\EventListenersQueueInterface;

final class EventListenersQueue implements EventListenersQueueInterface
{
    use TypedPlugsQueueTrait;

    public function accept(): string
    {
        return EventListenerInterface::class;
    }

    public function getPlugType(): PlugTypeInterface
    {
        return new EventListenerPlugType;
    }

    public function withAddedEventListener(EventListenerInterface $eventListener): EventListenersQueueInterface
    {
        $new = clone $this;
        $new->queue = $new->queue->withAddedPlug($eventListener);

        return $new;
    }
}
