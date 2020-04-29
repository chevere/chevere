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

use Chevere\Components\Hooks\HooksQueue;
use Chevere\Components\Hooks\HooksRunner;
use Chevere\Components\Hooks\Interfaces\HooksRunnerInterface;

trait HookableTrait
{
    private HooksRunnerInterface $hooksRunner;

    public function withHooksQueue(HooksQueue $hooksQueue): self
    {
        $new = clone $this;
        $new->hooksRunner = new HooksRunner($hooksQueue);

        return $new;
    }

    public function hook(string $anchor, &$argument): void
    {
        if (isset($this->hooksRunner) === false) {
            return;
        }
        $this->hooksRunner->run($anchor, $argument);
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
