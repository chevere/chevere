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

namespace Chevere\Components\Hooks;

use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Plugs\PlugsQueue;

/**
 * @method array toArray() [for => [priority => hookName,],]
 */
final class HooksQueue extends PlugsQueue
{
    public function withAdded(HookInterface $hook): HooksQueue
    {
        $new = clone $this;
        $new = $new->withAddedPlug($hook);

        return $new;
    }
}
