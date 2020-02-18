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

use Chevere\Components\Hooks\Queue;
use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Instances\HooksInstance;

trait HookableTrait
{
    private Queue $hooksQueue;

    private function setHooksQueue(bool $trace)
    {
        $hooks = HooksInstance::get();
        if ($hooks->has(__CLASS__)) {
            $this->hooksQueue = $hooks->queue(__CLASS__)->withTrace($trace);
        }
    }

    /**
     * Run the hooks queue for the given anchor (if-any).
     */
    private function hook(string $anchor): void
    {
        if (
            isset($this->hooksQueue) === false
            || is_a(
                debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['class'],
                HookInterface::class,
                true
            ) === true
        ) {
            return;
        }
        $this->hooksQueue->run($this, $anchor);
    }
}
