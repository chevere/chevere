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

namespace Chevere\Pluggable\Plug\Event;

use Chevere\Message\Message;
use Chevere\Pluggable\Interfaces\Plug\Event\EventInterface;
use Chevere\Pluggable\Interfaces\Plug\Event\EventQueueInterface;
use Chevere\Pluggable\Interfaces\Plug\Event\EventsRunnerInterface;
use Chevere\Pluggable\Interfaces\PlugsQueueInterface;
use Chevere\Throwable\Exceptions\RuntimeException;
use Chevere\Writer\Interfaces\WritersInterface;
use Throwable;

final class EventsRunner implements EventsRunnerInterface
{
    private PlugsQueueInterface $plugsQueue;

    private EventInterface $event;

    public function __construct(
        EventQueueInterface $queue,
        private WritersInterface $writers
    ) {
        $this->plugsQueue = $queue->plugsQueue();
    }

    public function run(string $anchor, array $data): void
    {
        try {
            $queue = $this->plugsQueue->toArray()[$anchor] ?? [];
            foreach ($queue as $entries) {
                foreach ($entries as $entry) {
                    // @codeCoverageIgnoreStart
                    /** @var EventInterface $this */
                    $this->event = new $entry();
                    // @codeCoverageIgnoreEnd
                    $runEvent = $this->event;
                    $runEvent($data, $this->writers);
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
