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

namespace Chevere\Pluggable\Interfaces\Plug\Event;

use Chevere\Pluggable\Interfaces\PlugsQueueTypedInterface;

/**
 * Describes the component in charge of type-hint an event listeners queue.
 */
interface EventQueueInterface extends PlugsQueueTypedInterface
{
}
