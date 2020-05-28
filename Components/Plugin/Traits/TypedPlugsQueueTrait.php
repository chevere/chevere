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

namespace Chevere\Components\Plugin\Traits;

use Chevere\Components\Message\Message;
use Chevere\Components\Plugin\PlugsQueue;
use Chevere\Exceptions\Plugin\PlugInterfaceException;
use Chevere\Interfaces\Plugin\PlugsQueueInterface;

trait TypedPlugsQueueTrait
{
    private PlugsQueueInterface $queue;

    final public function __construct(PlugsQueueInterface $queue)
    {
        $this->queue = $queue;
        if ($queue->plugType()->interface() !== $this->accept()) {
            throw new PlugInterfaceException(
                (new Message('Expecting a plugs queue for plugs of type %accept%, type %plugInterface% provided'))
                    ->code('%accept%', $this->accept())
                    ->code('%plugInterface%', $queue->plugType()->interface())
            );
        }
    }

    /**
     * @return string The accepted plug interface.
     */
    abstract public function accept(): string;

    public function queue(): PlugsQueueInterface
    {
        return $this->queue;
    }
}
