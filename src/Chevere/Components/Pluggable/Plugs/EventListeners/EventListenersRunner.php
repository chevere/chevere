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

namespace Chevere\Components\Pluggable\Plugs\EventListeners;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Pluggable\Plugs\EventListener\EventListenerInterface;
use Chevere\Interfaces\Pluggable\Plugs\EventListener\EventListenersQueueInterface;
use Chevere\Interfaces\Pluggable\Plugs\EventListener\EventListenersRunnerInterface;
use Chevere\Interfaces\Pluggable\PlugsQueueInterface;
use Chevere\Interfaces\Writer\WritersInterface;
use Throwable;

final class EventListenersRunner implements EventListenersRunnerInterface
{
    private PlugsQueueInterface $plugsQueue;

    private WritersInterface $writers;

    private EventListenerInterface $eventListener;

    public function __construct(EventListenersQueueInterface $queue, WritersInterface $writers)
    {
        $this->plugsQueue = $queue->plugsQueue();
        $this->writers = $writers;
    }

    public function run(string $anchor, array $data): void
    {
        try {
            $queue = $this->plugsQueue->toArray()[$anchor] ?? [];
            foreach ($queue as $entries) {
                foreach ($entries as $entry) {
                    // @codeCoverageIgnoreStart
                    /** @var EventListenerInterface */
                    $this->eventListener = new $entry();
                    // @codeCoverageIgnoreEnd
                    $event = $this->eventListener;
                    $event($data, $this->writers);
                }
            }
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new RuntimeException(
                (new Message('Unable to run event listeners for anchor %anchor% provided.'))
                    ->code('%anchor%', $anchor),
            );
        }
        // @codeCoverageIgnoreEnd
    }
}
