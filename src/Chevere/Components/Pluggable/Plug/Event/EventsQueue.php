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

namespace Chevere\Components\Pluggable\Plug\Event;

use Chevere\Components\Pluggable\Traits\TypedPlugsQueueTrait;
use Chevere\Components\Pluggable\Types\EventPlugType;
use Chevere\Interfaces\Pluggable\Plug\Event\EventInterface;
use Chevere\Interfaces\Pluggable\Plug\Event\EventQueueInterface;
use Chevere\Interfaces\Pluggable\PlugTypeInterface;

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
