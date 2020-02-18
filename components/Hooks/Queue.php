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

/**
 * Queue handler for Hooks registered in HookeableInterface.
 */
final class Queue
{
    /** @var array anchor => [0 => [HookInterface,]] */
    private array $queue = [];

    private array $trace;

    private HookInterface $hook;

    public function __construct(array $queue)
    {
        $this->queue = $queue;
    }

    public function withTrace(): Queue
    {
        $new = clone $this;
        $this->trace = [];

        return $new;
    }

    public function hasTrace(): bool
    {
        return isset($this->trace);
    }

    public function trace(): array
    {
        return $this->trace;
    }

    /**
     * Run the registred hooks at the given anchor.
     */
    public function run(object $object, string $anchor)
    {
        $anchor = $this->queue[$anchor] ?? null;
        if ($this->queue === null) {
            return;
        }
        // if ($this->trace !== null) {
        //     $this->trace['base'] = $object;
        // }
        foreach ($anchor as $entries) {
            foreach ($entries as $entry) {
                $this->hook = new $entry;
                ($this->hook)($object);
                // if (null !== $this->trace) {
                //     $this->trace[$entry['callable']] = $object;
                // }
            }
        }
    }
}
