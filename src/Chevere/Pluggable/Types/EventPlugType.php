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

namespace Chevere\Pluggable\Types;

use Chevere\Pluggable\Plug\Event\EventsQueue;
use Chevere\Pluggable\Interfaces\Plug\Event\EventInterface;
use Chevere\Pluggable\Interfaces\Plug\Event\EventQueueInterface;
use Chevere\Pluggable\Interfaces\Plug\Event\PluggableEventsInterface;
use Chevere\Pluggable\Interfaces\PlugTypeInterface;

/**
 * Describes the component in charge of defining a plug ot type event listener.
 */
final class EventPlugType implements PlugTypeInterface
{
    public function interface(): string
    {
        return EventInterface::class;
    }

    public function plugsTo(): string
    {
        return PluggableEventsInterface::class;
    }

    public function trailingName(): string
    {
        return 'Event.php';
    }

    public function getPlugsQueueTyped(): EventQueueInterface
    {
        return new EventsQueue();
    }

    public function pluggableAnchorsMethod(): string
    {
        return 'getEventAnchors';
    }
}
