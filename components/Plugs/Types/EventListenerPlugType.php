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

namespace Chevere\Components\Plugs\Types;

use Chevere\Components\Events\Interfaces\EventableInterface;
use Chevere\Components\Events\Interfaces\EventListenerInterface;
use Chevere\Components\Plugs\Interfaces\PlugTypeInterface;

final class EventListenerPlugType implements PlugTypeInterface
{
    public function interface(): string
    {
        return EventListenerInterface::class;
    }

    public function plugsTo(): string
    {
        return EventableInterface::class;
    }

    public function trailingName(): string
    {
        return 'EventListener.php';
    }

    public function plugableAnchorsMethod(): string
    {
        return 'getEventAnchors';
    }
}
