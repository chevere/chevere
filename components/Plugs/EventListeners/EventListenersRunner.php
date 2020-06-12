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

namespace Chevere\Components\Plugs\EventListeners;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Plugin\PlugsQueueInterface;
use Chevere\Interfaces\Plugs\EventListener\EventListenersQueueInterface;
use Chevere\Interfaces\Plugs\EventListener\EventListenersRunnerInterface;
use Chevere\Interfaces\Writers\WritersInterface;
use Throwable;

final class EventListenersRunner implements EventListenersRunnerInterface
{
    private PlugsQueueInterface $queue;

    private WritersInterface $writers;

    public function __construct(EventListenersQueueInterface $queue, WritersInterface $writers)
    {
        $this->queue = $queue->queue();
        $this->writers = $writers;
    }

    public function run(string $anchor, array $data): void
    {
        $queue = $this->queue->toArray()[$anchor] ?? [];
        foreach ($queue as $entries) {
            foreach ($entries as $entry) {
                // @codeCoverageIgnoreStart
                try {
                    $this->eventListener = new $entry;
                } catch (Throwable $e) {
                    throw new RuntimeException(
                        (new Message('Invalid event listener type'))
                    );
                }
                // @codeCoverageIgnoreEnd
                $eventListener = $this->eventListener;
                $eventListener($data, $this->writers);
            }
        }
    }
}
