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
use Chevere\Components\Hooks\Interfaces\HooksRunnerInterface;

/**
 * Queue handler for Hooks registered for a given HookeableInterface.
 */
final class HooksRunner implements HooksRunnerInterface
{
    private HooksQueue $queue;

    public function __construct(HooksQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Run the registred hooks at the given anchor.
     */
    public function run(object $object, string $anchor): object
    {
        if ($this->isLooping()) {
            return $object;
        }
        $queue = $this->queue->toArray()[$anchor] ?? null;
        if ($queue === null) {
            return $object;
        }
        // if ($this->trace !== null) {
        //     $this->trace['base'] = $object;
        // }
        foreach ($queue as $entries) {
            foreach ($entries as $entry) {
                if (is_a($entry, HookInterface::class, true)) {
                    /**
                     * @var HookInterface $entry
                     */
                    $hook = new $entry;
                    $object = $hook($object);
                    // xd($entry);
                    // if ($object === null) {
                    //     xdd($queue, $anchor);
                    // }
                }
                // if (null !== $this->trace) {
                //     $this->trace[$entry['callable']] = $object;
                // }
            }
        }

        return $object;
    }

    private function isLooping(): bool
    {
        return is_a(
            debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)[4]['class'],
            HookInterface::class,
            true
        );
    }
}
