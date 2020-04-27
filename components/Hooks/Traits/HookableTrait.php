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
use Chevere\Components\Hooks\Interfaces\HooksQueueInterface;
use Chevere\Components\Instances\HooksInstance;
use LogicException;

trait HookableTrait
{
    private HooksQueueInterface $hooksQueue;

    public function withHooksQueue(HooksQueueInterface $hooksQueue): self
    {
        $new = clone $this;
        $new->hooksQueue = $hooksQueue;

        return $new;
    }

    public function hook(string $anchor): void
    {
        if (isset($this->hooksQueue) === false) {
            return;
        }
        $this->hooksQueue->run($this, $anchor);
    }

    // public function getHooksQueue(): HooksQueueInterface
    // {
    //     $hooksQueue = new HooksQueueNull;
    //     try {
    //         $hooks = HooksInstance::get();
    //     } catch (LogicException $e) {
    //         return $hooksQueue;
    //     }
    //     if (isset($hooks) && $hooks->has(static::class)) {
    //         return $hooks->queue(static::class);
    //     }

    //     return $hooksQueue;
    // }
}
