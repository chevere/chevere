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

namespace Chevere\Pluggable\Plug\Event;

use Chevere\Pluggable\Interfaces\Plug\Event\EventInterface;
use Chevere\Pluggable\Interfaces\Plug\Event\EventQueueInterface;
use Chevere\Pluggable\Interfaces\PlugTypeInterface;
use Chevere\Pluggable\Traits\TypedPlugsQueueTrait;
use Chevere\Pluggable\Types\EventPlugType;

final class EventsQueue implements EventQueueInterface
{
    use TypedPlugsQueueTrait;

    public function interface(): string
    {
        return EventInterface::class;
    }

    public function getPlugType(): PlugTypeInterface
    {
        return new EventPlugType();
    }
}
