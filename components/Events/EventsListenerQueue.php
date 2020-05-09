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

namespace Chevere\Components\Events;

use Chevere\Components\Events\Interfaces\EventListenerInterface;
use Chevere\Components\Plugs\PlugsQueue;

/**
 * @method array toArray() [for => [priority => eventListenerName,],]
 */
final class EventsListenerQueue extends PlugsQueue
{
    public function withAdded(EventListenerInterface $eventListener): EventsListenerQueue
    {
        $new = clone $this;
        $new = $new->withAddedPlug($eventListener);

        return $new;
    }
}
