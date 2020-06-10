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

namespace Chevere\Components\Plugs\EventListeners;

use Chevere\Components\Plugin\Traits\TypedPlugsQueueTrait;
use Chevere\Interfaces\Plugin\TypedPlugsQueueInterface;
use Chevere\Interfaces\Plugs\EventListener\EventListenerInterface;

final class EventListenersQueue implements TypedPlugsQueueInterface
{
    use TypedPlugsQueueTrait;

    public function accept(): string
    {
        return EventListenerInterface::class;
    }
}
