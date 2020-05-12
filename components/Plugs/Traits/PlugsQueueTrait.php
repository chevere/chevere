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

namespace Chevere\Components\Plugs\Traits;

use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Interfaces\PlugsQueueInterface;
use Chevere\Components\Plugs\PlugsQueue;
use LogicException;

trait PlugsQueueTrait
{
    private PlugsQueueInterface $queue;

    final public function __construct(PlugsQueueInterface $queue)
    {
        $this->queue = $queue;
        if ($queue->plugType()->interface() !== $this->accept()) {
            throw new LogicException(
                (new Message('Expecting a queue for plugs of type %accept%, type %provided% provided'))
                    ->code('%accept%', $this->accept())
                    ->code('%provided%', $queue->plugType()->interface())
                    ->toString()
            );
        }
    }

    abstract public function accept(): string;

    public function queue(): PlugsQueue
    {
        return $this->queue;
    }
}
