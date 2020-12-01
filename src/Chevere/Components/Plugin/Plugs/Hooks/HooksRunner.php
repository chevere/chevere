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

namespace Chevere\Components\Plugin\Plugs\Hooks;

use Chevere\Components\Message\Message;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Plugin\Plugs\Hooks\HookInterface;
use Chevere\Interfaces\Plugin\Plugs\Hooks\HooksQueueInterface;
use Chevere\Interfaces\Plugin\Plugs\Hooks\HooksRunnerInterface;
use Chevere\Interfaces\Plugin\PlugsQueueInterface;
use Throwable;

final class HooksRunner implements HooksRunnerInterface
{
    private PlugsQueueInterface $plugsQueue;

    private HookInterface $hook;

    public function __construct(HooksQueueInterface $hooksQueue)
    {
        $this->plugsQueue = $hooksQueue->plugsQueue();
    }

    public function run(string $anchor, &$argument): void
    {
        // if ($this->isLooping()) {
        //     return;
        // }
        $queue = $this->plugsQueue->toArray()[$anchor] ?? [];
        $gettype = $this->getType($argument);
        $type = new Type($gettype);
        foreach ($queue as $entries) {
            foreach ($entries as $entry) {
                $this->setHook($entry);
                $hook = $this->hook;
                $hook($argument);
                if (!$type->validate($argument)) {
                    throw new InvalidArgumentException(
                        (new Message('Hook argument of type %passed% has been altered to type %altered% by %hook%'))
                            ->code('%passed%', $gettype)
                            ->code('%altered%', gettype($argument))
                            ->code('%hook%', get_class($this->hook))
                    );
                }
            }
        }
    }

    private function getType($argument): string
    {
        $gettype = gettype($argument);
        if ($gettype === 'object') {
            return get_class($argument);
        }

        return $gettype;
    }

    /**
     * @codeCoverageIgnore
     */
    private function setHook(string $entry): void
    {
        try {
            $this->hook = new $entry;
        } catch (Throwable $e) {
            throw new RuntimeException(
                (new Message('Invalid hook type'))
            );
        }
    }

    // private function isLooping(): bool
    // {
    //     return is_a(
    //         debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)[4]['class'],
    //         HookInterface::class,
    //         true
    //     );
    // }
}
