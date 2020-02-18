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

namespace Chevere\Components\Hooks\Traits;

use Chevere\Components\Hooks\Hooks;
use Chevere\Components\Hooks\HooksQueue;
use Chevere\Components\Hooks\HooksQueueNull;
use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Hooks\Interfaces\HooksQueueInterface;
use Chevere\Components\Instances\HooksInstance;

trait HookableTrait
{
    private HooksQueueInterface $hooksQueue;

    private function prepareHooks(Hooks $hooks)
    {
        $this->hooksQueue = $hooks->has(static::class)
            ? $hooks->queue(static::class)
            : new HooksQueueNull();
    }

    /**
     * Run the hooks queue for the given anchor (if-any).
     */
    private function hook(string $anchor): void
    {
        if (isset($this->hooksQueue) === false) {
            return; // @codeCoverageIgnore
        }
        $class = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['class'];
        if (is_a($class, HookInterface::class, true) === true) {
            return;
        }
        $this->hooksQueue->run($this, $anchor);
    }
}
