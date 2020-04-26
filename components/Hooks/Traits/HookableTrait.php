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
    private HooksQueueInterface $queue;

    public function hook(string $anchor): void
    {
        if (isset($this->queue) === false) {
            $this->setQueue();
        }
        $this->queue->run($this, $anchor);
    }

    private function setQueue()
    {
        $this->queue = HooksInstance::get()->has(static::class)
            ? HooksInstance::get()->queue(static::class)
            : new HooksQueueNull();
    }
}
