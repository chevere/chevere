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

namespace Chevere\Components\Pluggable\Types;

use Chevere\Components\Pluggable\Plugs\EventListeners\EventListenersQueue;
use Chevere\Interfaces\Pluggable\Plugs\EventListener\EventListenerInterface;
use Chevere\Interfaces\Pluggable\Plugs\EventListener\EventListenersQueueInterface;
use Chevere\Interfaces\Pluggable\Plugs\EventListener\PluggableEventsInterface;
use Chevere\Interfaces\Pluggable\PlugTypeInterface;

/**
 * Describes the component in charge of defining a plug ot type event listener.
 */
final class EventListenerPlugType implements PlugTypeInterface
{
    public function interface(): string
    {
        return EventListenerInterface::class;
    }

    public function plugsTo(): string
    {
        return PluggableEventsInterface::class;
    }

    public function trailingName(): string
    {
        return 'EventListener.php';
    }

    public function getPlugsQueueTyped(): EventListenersQueueInterface
    {
        return new EventListenersQueue();
    }

    public function pluggableAnchorsMethod(): string
    {
        return 'getEventAnchors';
    }
}
