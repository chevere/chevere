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

namespace Chevere\Pluggable\Plug\Hook;

use Chevere\Message\Message;
use Chevere\Pluggable\Interfaces\Plug\Hook\HookInterface;
use Chevere\Pluggable\Interfaces\Plug\Hook\HooksQueueInterface;
use Chevere\Pluggable\Interfaces\Plug\Hook\HooksRunnerInterface;
use Chevere\Pluggable\Interfaces\PlugsQueueInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\RuntimeException;
use Chevere\Type\Type;
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
                            ->code('%altered%', get_debug_type($argument))
                            ->code('%hook%', $this->hook::class)
                    );
                }
            }
        }
    }

    private function getType($argument): string
    {
        $gettype = gettype($argument);
        if ($gettype === 'object') {
            return $argument::class;
        }

        return $gettype;
    }

    /**
     * @codeCoverageIgnore
     */
    private function setHook(string $entry): void
    {
        try {
            /** @var HookInterface $this */
            $this->hook = new $entry();
        } catch (Throwable $e) {
            throw new RuntimeException(
                (new Message('Invalid hook type'))
            );
        }
    }
}
