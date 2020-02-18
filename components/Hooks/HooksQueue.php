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
use Chevere\Components\Hooks\Interfaces\HooksQueueInterface;

/**
 * Queue handler for Hooks registered for a given HookeableInterface.
 */
final class HooksQueue implements HooksQueueInterface
{
    /** @var array anchor => [0 => [HookInterface,]] */
    private array $anchors = [];

    public function __construct(array $anchors)//, $observer
    {
        $this->anchors = $anchors;
    }

    /**
     * Run the registred hooks at the given anchor.
     */
    public function run(object $object, string $anchor): void
    {
        $anchor = $this->anchors[$anchor] ?? null;
        if ($anchor === null) {
            return;
        }
        // if ($this->trace !== null) {
        //     $this->trace['base'] = $object;
        // }
        foreach ($anchor as $entries) {
            foreach ($entries as $entry) {
                if (is_a($entry, HookInterface::class, true)) {
                    $hook = new $entry;
                    $hook($object);
                }
                // if (null !== $this->trace) {
                //     $this->trace[$entry['callable']] = $object;
                // }
            }
        }
    }
}
