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

namespace Chevere\Components\Pluggable\Plug\Event;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Pluggable\Plug\Event\EventInterface;
use Chevere\Interfaces\Pluggable\Plug\Event\EventQueueInterface;
use Chevere\Interfaces\Pluggable\Plug\Event\EventsRunnerInterface;
use Chevere\Interfaces\Pluggable\PlugsQueueInterface;
use Chevere\Interfaces\Writer\WritersInterface;
use Throwable;

final class EventsRunner implements EventsRunnerInterface
{
    private PlugsQueueInterface $plugsQueue;

    private WritersInterface $writers;

    private EventInterface $event;

    public function __construct(EventQueueInterface $queue, WritersInterface $writers)
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
