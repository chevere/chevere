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

namespace Chevere\Components\Plugin\Plugs\EventListeners;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Plugin\Plugs\EventListener\EventListenerInterface;
use Chevere\Interfaces\Plugin\Plugs\EventListener\EventListenersQueueInterface;
use Chevere\Interfaces\Plugin\Plugs\EventListener\EventListenersRunnerInterface;
use Chevere\Interfaces\Plugin\PlugsQueueInterface;
use Chevere\Interfaces\Writer\WritersInterface;
use Throwable;

final class EventListenersRunner implements EventListenersRunnerInterface
{
    private PlugsQueueInterface $queue;

    private WritersInterface $writers;

    private EventListenerInterface $eventListener;

    public function __construct(EventListenersQueueInterface $queue, WritersInterface $writers)
    {
        $this->queue = $queue->plugsQueue();
        $this->writers = $writers;
    }

    public function run(string $anchor, array $data): void
    {
        try {
            $queue = $this->queue->toArray()[$anchor] ?? [];
            foreach ($queue as $entries) {
                foreach ($entries as $entry) {
                    // @codeCoverageIgnoreStart
                    $this->eventListener = new $entry;
                    // @codeCoverageIgnoreEnd
                    $eventListener = $this->eventListener;
                    $eventListener($data, $this->writers);
                }
            }
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new RuntimeException(
                (new Message('Unable to run event listeners for anchor %anchor% provided.'))
                    ->code('%anchor%', $anchor),
                0,
                $e
            );
        }
        // @codeCoverageIgnoreEnd
    }
}
