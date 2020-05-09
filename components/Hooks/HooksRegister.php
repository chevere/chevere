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
use Chevere\Components\Plugs\PlugsRegister;

final class HooksRegister extends PlugsRegister
{
    public function withAddedHook(HookInterface $hook): HooksRegister
    {
        $this->assertNoOverride($hook);
        $hooksQueue = $this->map->hasKey($hook->at())
            ? $this->map->get($hook->at())
            : new HooksQueue;
        $new = clone $this;
        $new->map->put($hook->at(), $hooksQueue->withHook($hook));

        return $new;
    }
}
